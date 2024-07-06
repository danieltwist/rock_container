<?php

namespace Database\Seeders;

use App\Models\InvoiceTemplate;
use Illuminate\Database\Seeder;

class InvoiceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceTemplate::create([
            'name' => 'По умолчанию',
            'file' => 'public/templates/invoice_template.docx',
            'info' => 'Основной шаблон инвойса'
        ]);
    }
}
