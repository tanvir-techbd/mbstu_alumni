<?php

namespace Database\Support;

/**
 * Seed-only utility: generates realistic Bangladeshi names (Latin
 * transliteration, matching how they'd actually appear in an English-language
 * university system) instead of Faker's default en_US names. Never used at
 * runtime — only from DatabaseSeeder/factories.
 */
class BangladeshiNameGenerator
{
    private const MALE_FIRST_NAMES = [
        'Tanvir', 'Rakibul', 'Shakil', 'Mahmudul', 'Rafiul', 'Nayeem', 'Shariar',
        'Imtiaz', 'Fahim', 'Asif', 'Zahid', 'Kamrul', 'Mehedi', 'Rubel', 'Shovon',
        'Arafat', 'Sajid', 'Nahid', 'Jubayer', 'Rezaul', 'Habibur', 'Shahriar',
        'Golam', 'Ashraful', 'Tarequl', 'Rashedul', 'Firoz', 'Mizanur', 'Anisur',
        'Faisal',
    ];

    private const FEMALE_FIRST_NAMES = [
        'Nusrat', 'Fahmida', 'Sharmin', 'Tania', 'Sabrina', 'Farzana', 'Rummana',
        'Ismat', 'Jannatul', 'Marzia', 'Shammi', 'Tanjina', 'Rifat', 'Mahiya',
        'Afsana', 'Lamia', 'Tasnim', 'Nabila', 'Sadia', 'Rukhsana', 'Sumaiya',
        'Israt', 'Nazia', 'Shirin', 'Taslima', 'Kaniz', 'Rehana', 'Munira',
        'Anika', 'Proma',
    ];

    private const LAST_NAMES = [
        'Ahmed', 'Rahman', 'Islam', 'Hossain', 'Khan', 'Chowdhury', 'Akter',
        'Uddin', 'Alam', 'Karim', 'Hasan', 'Sarkar', 'Talukder', 'Molla',
        'Sikder', 'Bhuiyan', 'Miah', 'Reza', 'Haque', 'Kabir',
    ];

    public static function male(): string
    {
        $name = self::MALE_FIRST_NAMES[array_rand(self::MALE_FIRST_NAMES)].' '.self::LAST_NAMES[array_rand(self::LAST_NAMES)];

        return random_int(1, 100) <= 40 ? 'Md. '.$name : $name;
    }

    public static function female(): string
    {
        return self::FEMALE_FIRST_NAMES[array_rand(self::FEMALE_FIRST_NAMES)].' '.self::LAST_NAMES[array_rand(self::LAST_NAMES)];
    }

    /**
     * @return array{name: string, gender: 'male'|'female'}
     */
    public static function random(): array
    {
        $gender = random_int(0, 1) === 0 ? 'male' : 'female';

        return [
            'name' => $gender === 'male' ? self::male() : self::female(),
            'gender' => $gender,
        ];
    }

    /**
     * Best-effort gender guess from a name, for names that didn't originate
     * from random() (e.g. fixed demo accounts) — falls back to a coin flip.
     */
    public static function guessGender(string $name): string
    {
        $firstWord = trim(preg_replace('/^Md\.\s*/i', '', $name));
        $firstWord = explode(' ', $firstWord)[0] ?? '';

        if (in_array($firstWord, self::MALE_FIRST_NAMES, true)) {
            return 'male';
        }

        if (in_array($firstWord, self::FEMALE_FIRST_NAMES, true)) {
            return 'female';
        }

        return random_int(0, 1) === 0 ? 'male' : 'female';
    }
}
