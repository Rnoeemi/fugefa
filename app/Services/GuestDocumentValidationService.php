<?php

namespace App\Services;

use App\Enums\GuestStatus;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\GuestDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GuestDocumentValidationService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function validateAndStore(Guest $guest, ?Booking $booking, array $payload): GuestDocument
    {
        return $this->storeValidatedDocument($guest, $booking, [
            'name' => $guest->name,
            'id_number' => $guest->id_number,
            'id_card_front_path' => $payload['id_card_front_path'] ?? null,
            'id_card_back_path' => $payload['id_card_back_path'] ?? null,
            'address_card_front_path' => $payload['address_card_front_path'] ?? null,
            'document_number' => $payload['document_number'] ?? null,
            'full_name_on_document' => $payload['full_name_on_document'] ?? null,
            'birth_date' => $payload['birth_date'] ?? null,
            'nationality' => $payload['nationality'] ?? 'HU',
            'address_on_card' => $payload['address_on_card'] ?? null,
        ], requireDocuments: true);
    }

    /**
     * @param  list<array<string, mixed>>  $travelers
     * @return list<GuestDocument>
     */
    public function validateTravelers(Guest $booker, Booking $booking, array $travelers): array
    {
        $documents = [];
        $travelerGuestIds = [];

        foreach ($travelers as $index => $traveler) {
            $birthDate = $traveler['birth_date'] ?? null;

            if (blank($birthDate) || blank($traveler['name'] ?? null)) {
                throw ValidationException::withMessages([
                    "travelers.{$index}.name" => 'Minden vendég neve és születési dátuma kötelező.',
                ]);
            }

            $age = Carbon::parse($birthDate)->age;
            $requiresDocuments = $age >= 14;

            if (! $requiresDocuments) {
                continue;
            }

            $guest = $this->resolveTravelerGuest($booker, $traveler, $index);
            $travelerGuestIds[$index] = $guest->id;

            $stored = $this->findReusableDocument($guest, $traveler['id_number'] ?? null);

            if ($stored && (
                filled($traveler['known_guest_id'] ?? null)
                || filled($traveler['documents_on_file'] ?? null)
                || blank($traveler['id_card_front_path'] ?? null)
            )) {
                $documents[] = $this->attachStoredDocument($guest, $booking, $stored, $traveler, $index);

                continue;
            }

            $documents[] = $this->storeValidatedDocument($guest, $booking, [
                'name' => $traveler['name'],
                'id_number' => $traveler['id_number'] ?? null,
                'id_card_front_path' => $traveler['id_card_front_path'] ?? null,
                'id_card_back_path' => $traveler['id_card_back_path'] ?? null,
                'address_card_front_path' => $traveler['address_card_front_path'] ?? null,
                'document_number' => $traveler['id_number'] ?? $traveler['document_number'] ?? null,
                'full_name_on_document' => $traveler['full_name_on_document'] ?? $traveler['name'],
                'birth_date' => $birthDate,
                'nationality' => $traveler['nationality'] ?? 'HU',
                'address_on_card' => $traveler['address_on_card'] ?? null,
                'needs_manual_validation' => (bool) ($traveler['needs_manual_validation'] ?? false),
                'manual_validation_reasons' => $traveler['manual_validation_reasons'] ?? [],
            ], requireDocuments: true, travelerIndex: $index);
        }

        if ($travelerGuestIds !== []) {
            $breakdown = $booking->price_breakdown ?? [];
            $metaTravelers = $breakdown['travelers'] ?? [];

            foreach ($travelerGuestIds as $index => $guestId) {
                if (! isset($metaTravelers[$index]) || ! is_array($metaTravelers[$index])) {
                    continue;
                }

                $metaTravelers[$index]['guest_id'] = $guestId;
            }

            $breakdown['travelers'] = $metaTravelers;
            $booking->update(['price_breakdown' => $breakdown]);
        }

        return $documents;
    }

    public function findReusableDocument(Guest $guest, ?string $idNumber = null): ?GuestDocument
    {
        $stored = $guest->latestDocumentWithImages();

        if ($stored) {
            return $stored;
        }

        if (blank($idNumber)) {
            return null;
        }

        $known = Guest::findByIdNumber((string) $idNumber);

        return $known?->latestDocumentWithImages();
    }

    /**
     * @param  array<string, mixed>  $traveler
     */
    protected function attachStoredDocument(
        Guest $guest,
        Booking $booking,
        GuestDocument $source,
        array $traveler,
        ?int $travelerIndex = null,
    ): GuestDocument {
        $document = GuestDocument::query()->create([
            'guest_id' => $guest->id,
            'booking_id' => $booking->id,
            'id_card_front_path' => $source->id_card_front_path,
            'id_card_back_path' => $source->id_card_back_path,
            'address_card_front_path' => $source->address_card_front_path,
            'document_number' => $traveler['id_number'] ?? $source->document_number,
            'full_name_on_document' => $traveler['full_name_on_document'] ?? $source->full_name_on_document ?? $traveler['name'] ?? $guest->name,
            'birth_date' => $traveler['birth_date'] ?? $source->birth_date,
            'nationality' => $traveler['nationality'] ?? $source->nationality ?? 'HU',
            'address_on_card' => $traveler['address_on_card'] ?? $source->address_on_card,
            'name_matches' => true,
            'id_number_matches' => true,
            'address_present' => filled($traveler['address_on_card'] ?? $source->address_on_card),
            'is_validated' => true,
            'ntak_ready' => filled($traveler['birth_date'] ?? $source->birth_date)
                && filled($traveler['nationality'] ?? $source->nationality),
            'validation_notes' => ['Ismert vendég – korábbi okmányképek újrafelhasználva.'],
        ]);

        if ($travelerIndex === 0 && filled($document->document_number) && blank($guest->id_number)) {
            $guest->update(['id_number' => $document->document_number]);
        }

        return $document;
    }

    /**
     * Foglaló = meglévő Guest; további 14+ vendégekhez saját Guest profil (okmányokkal).
     *
     * @param  array<string, mixed>  $traveler
     */
    protected function resolveTravelerGuest(Guest $booker, array $traveler, int $index): Guest
    {
        if (filled($traveler['known_guest_id'] ?? null)) {
            $known = Guest::query()->find($traveler['known_guest_id']);

            if ($known) {
                if ($index === 0) {
                    if (filled($traveler['id_number'] ?? null) && blank($booker->id_number)) {
                        $booker->id_number = $traveler['id_number'];
                    }

                    if (blank($booker->name) && filled($known->name)) {
                        $booker->name = $known->name;
                    }

                    $booker->save();

                    return $booker;
                }

                $known->fill([
                    'name' => $traveler['name'] ?? $known->name,
                    'id_number' => $traveler['id_number'] ?? $known->id_number,
                ])->save();

                return $known;
            }
        }

        if ($index === 0) {
            if (filled($traveler['id_number'] ?? null) && blank($booker->id_number)) {
                $booker->id_number = $traveler['id_number'];
                $booker->save();
            }

            return $booker;
        }

        $idNumber = filled($traveler['id_number'] ?? null) ? trim((string) $traveler['id_number']) : null;

        if ($idNumber !== null) {
            $existing = Guest::findByIdNumber($idNumber);

            if ($existing && $existing->id !== $booker->id) {
                $existing->fill([
                    'name' => $traveler['name'] ?? $existing->name,
                ])->save();

                return $existing;
            }
        }

        return Guest::query()->create([
            'name' => $traveler['name'],
            'email' => null,
            'phone' => null,
            'id_number' => $idNumber,
            'status' => $booker->status ?? GuestStatus::Welcome,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function storeValidatedDocument(
        Guest $guest,
        ?Booking $booking,
        array $payload,
        bool $requireDocuments = true,
        ?int $travelerIndex = null,
    ): GuestDocument {
        $document = new GuestDocument([
            'guest_id' => $guest->id,
            'booking_id' => $booking?->id,
            'id_card_front_path' => $payload['id_card_front_path'] ?? null,
            'id_card_back_path' => $payload['id_card_back_path'] ?? null,
            'address_card_front_path' => $payload['address_card_front_path'] ?? null,
            'document_number' => $payload['document_number'] ?? null,
            'full_name_on_document' => $payload['full_name_on_document'] ?? null,
            'birth_date' => $payload['birth_date'] ?? null,
            'nationality' => $payload['nationality'] ?? 'HU',
            'address_on_card' => $payload['address_on_card'] ?? null,
        ]);

        $notes = [];
        $prefix = $travelerIndex !== null ? "travelers.{$travelerIndex}." : '';

        $nameOnDoc = $this->normalize((string) ($document->full_name_on_document ?? ''));
        $personName = $this->normalize((string) ($payload['name'] ?? $guest->name ?? ''));
        $document->name_matches = filled($nameOnDoc) && filled($personName) && (
            $nameOnDoc === $personName
            || Str::contains($nameOnDoc, $personName)
            || Str::contains($personName, $nameOnDoc)
        );

        if (! $document->name_matches) {
            $notes[] = 'A megadott név és az igazolványon szereplő név nem egyezik.';
        }

        $docNumber = $this->normalize((string) ($document->document_number ?? ''));
        $personIdNumber = $this->normalize((string) ($payload['id_number'] ?? ''));
        $document->id_number_matches = filled($docNumber) && filled($personIdNumber) && $docNumber === $personIdNumber;

        if (! $document->id_number_matches) {
            $notes[] = 'A megadott igazolványszám és az igazolvány mező nem egyezik.';
        }

        $document->address_present = filled($document->address_on_card)
            && filled($document->address_card_front_path);

        if (! $document->address_present) {
            $notes[] = 'Lakcímkártya előlap vagy címadat hiányzik.';
        }

        $hasAllImages = filled($document->id_card_front_path)
            && filled($document->id_card_back_path)
            && filled($document->address_card_front_path);

        if (! $hasAllImages) {
            $notes[] = 'Mindhárom dokumentumkép kötelező (személyi előlap, hátlap, lakcímkártya előlap).';
        }

        $document->is_validated = $hasAllImages
            && $document->name_matches
            && $document->id_number_matches
            && $document->address_present;

        $document->ntak_ready = $document->is_validated
            && filled($document->birth_date)
            && filled($document->nationality);

        if (! $document->ntak_ready && $document->is_validated) {
            $notes[] = 'NTAK-hoz születési dátum és állampolgárság is szükséges.';
        }

        if (! empty($payload['needs_manual_validation'])) {
            $notes[] = 'Kézi validáció szükséges (OCR adateltérés).';
            foreach (($payload['manual_validation_reasons'] ?? []) as $reason) {
                if (filled($reason)) {
                    $notes[] = (string) $reason;
                }
            }
            // OCR eltérés esetén ne bukjon a foglalás – admin jelzés a notes-ban.
            $document->is_validated = $hasAllImages && $document->address_present;
            $document->ntak_ready = false;
        }

        $document->validation_notes = array_values(array_unique($notes));

        // OCR kézi validáció: ne dobjunk ValidationException-t az eltérés miatt.
        $blockOnMismatch = empty($payload['needs_manual_validation']);

        if ($requireDocuments && ! $document->is_validated && $blockOnMismatch) {
            throw ValidationException::withMessages([
                $prefix.'full_name_on_document' => implode(' ', $notes) ?: 'Az igazolvány adatok nem érvényesíthetők.',
            ]);
        }

        if ($requireDocuments && ! $hasAllImages) {
            throw ValidationException::withMessages([
                $prefix.'id_card_front_path' => 'Mindhárom dokumentumkép kötelező (személyi előlap, hátlap, lakcímkártya előlap).',
            ]);
        }

        $document->save();

        if ($travelerIndex === 0 && filled($document->document_number) && blank($guest->id_number)) {
            $guest->update(['id_number' => $document->document_number]);
        }

        return $document;
    }

    protected function normalize(string $value): string
    {
        $value = Str::lower(trim($value));
        $value = Str::ascii($value);

        return preg_replace('/\s+/', ' ', $value) ?? $value;
    }
}
