<?php

return [
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'array' => ':attribute harus berupa daftar.',
    'confirmed' => 'Konfirmasi :attribute tidak sesuai.',
    'date' => ':attribute harus berupa tanggal yang valid.',
    'distinct' => ':attribute memiliki nilai yang sama.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa file.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa bilangan bulat.',

    'max' => [
        'array' => ':attribute maksimal berisi :max pilihan.',
        'file' => 'Ukuran :attribute maksimal :max kilobita.',
        'numeric' => ':attribute maksimal bernilai :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],

    'mimes' => ':attribute harus berupa file dengan format: :values.',

    'min' => [
        'array' => ':attribute minimal berisi :min pilihan.',
        'file' => 'Ukuran :attribute minimal :min kilobita.',
        'numeric' => ':attribute minimal bernilai :min.',
        'string' => ':attribute minimal :min karakter.',
    ],

    'numeric' => ':attribute harus berupa angka.',
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'url' => ':attribute harus berupa URL yang valid.',

    'password' => [
        'letters' => ':attribute harus mengandung minimal satu huruf.',
        'mixed' => ':attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers' => ':attribute harus mengandung minimal satu angka.',
        'symbols' => ':attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':attribute pernah muncul dalam kebocoran data. Gunakan password lain.',
    ],

    'custom' => [
        'username' => [
            'min' => 'Username minimal 3 karakter.',
            'max' => 'Username maksimal 50 karakter.',
            'unique' => 'Username sudah digunakan.',
        ],

        'password' => [
            'min' => 'Password minimal 8 karakter.',
            'confirmed' => 'Konfirmasi password tidak sesuai.',
        ],

        'portfolio_file' => [
            'file' => 'Portofolio yang diunggah harus berupa file.',
            'max' => 'Ukuran file portofolio maksimal 10 MB.',
            'mimes' => 'File portofolio harus berformat PDF, JPG, JPEG, PNG, atau ZIP.',
            'uploaded' => 'File portofolio gagal diunggah. Periksa ukuran dan format file.',
        ],

        'skills' => [
            'required' => 'Pilih minimal satu bidang keahlian.',
            'array' => 'Bidang keahlian yang dipilih tidak valid.',
            'min' => 'Pilih minimal satu bidang keahlian.',
        ],

        'skills.*' => [
            'exists' => 'Salah satu bidang keahlian yang dipilih tidak tersedia.',
            'distinct' => 'Bidang keahlian tidak boleh dipilih lebih dari satu kali.',
        ],
    ],

    'attributes' => [
        'username' => 'username',
        'name' => 'nama lengkap',
        'email' => 'email',
        'phone' => 'nomor telepon',
        'password' => 'password',
        'password_confirmation' => 'konfirmasi password',
        'bio' => 'bio',
        'experience_years' => 'lama pengalaman',
        'portfolio_url' => 'link portofolio',
        'portfolio_file' => 'file portofolio',
        'skills' => 'bidang keahlian',
        'skills.*' => 'bidang keahlian',
    ],
];