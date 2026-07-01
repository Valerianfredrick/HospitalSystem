<?php

namespace App\Services;

class TriageService
{
    /**
     * specialty => keywords. First match wins, so more specific
     * specialties are listed before broader ones. 'general' is never
     * listed here — it's the fallback when nothing matches.
     */
    protected const SPECIALTY_KEYWORDS = [
        'dentistry' => [
            'tooth', 'teeth', 'gum', 'gums', 'cavity', 'cavities',
            'dental', 'molar', 'toothache',
        ],
        'orthopedics' => [
            'bone', 'fracture', 'joint', 'knee', 'hip pain', 'spine',
            'back pain', 'sprain', 'dislocation',
        ],
        'cardiology' => [
            'heart', 'chest pain', 'cardiac', 'palpitation',
            'hypertension', 'blood pressure',
        ],
        'pediatrics' => [
            'infant', 'newborn', 'pediatric',
        ],
        'dermatology' => [
            'skin', 'rash', 'acne', 'eczema', 'dermat',
        ],
        'ophthalmology' => [
            'eye', 'vision', 'cataract', 'conjunctivitis',
        ],
        'ent' => [
            'ear', 'nose', 'throat', 'sinus', 'tonsil',
        ],
        'gynecology' => [
            'pregnan', 'menstrual', 'gynec', 'obstetric',
        ],
        'psychiatry' => [
            'depression', 'anxiety', 'mental health', 'psychiatric',
        ],
    ];

    public function detectSpecialty(?string $diagnosis): string
    {
        $text = strtolower($diagnosis ?? '');

        if ($text === '') {
            return 'general';
        }

        foreach (self::SPECIALTY_KEYWORDS as $specialty => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $specialty;
                }
            }
        }

        return 'general';
    }

    /** Human-readable labels for dropdowns / badges. */
    public static function labels(): array
    {
        return [
            'general'       => 'General Medicine',
            'dentistry'     => 'Dentistry',
            'orthopedics'   => 'Orthopedics',
            'cardiology'    => 'Cardiology',
            'pediatrics'    => 'Pediatrics',
            'dermatology'   => 'Dermatology',
            'ophthalmology' => 'Ophthalmology',
            'ent'           => 'ENT (Ear, Nose, Throat)',
            'gynecology'    => 'Gynecology/Obstetrics',
            'psychiatry'    => 'Psychiatry',
        ];
    }
}
