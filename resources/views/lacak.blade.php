@extends('layouts.public')

@section('title', 'Lacak Status Permohonan - Bappeda PPID')

@section('content')
<main class="flex-grow w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-stack-lg flex flex-col gap-stack-lg">
<!-- Hero Section -->
<section class="text-center py-stack-lg flex flex-col items-center max-w-2xl mx-auto w-full">
<div class="bg-primary-container text-on-primary-container rounded-full w-16 h-16 flex items-center justify-center mb-stack-md">
<span class="material-symbols-outlined text-3xl" data-icon="search" style="font-variation-settings: 'FILL' 1;">search</span>
</div>
<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-stack-sm">Lacak Status Permohonan</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
                Masukkan nomor pendaftaran atau tiket Anda untuk mengetahui status terkini dari permohonan informasi publik yang telah diajukan.
            </p>
</section>

<!-- Tracking Input Section (Bento Style Card) -->
<section class="w-full max-w-3xl mx-auto">
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-stack-lg shadow-sm">
<form action="#" class="flex flex-col gap-stack-md" method="GET">
<label class="font-label-md text-label-md text-on-surface font-semibold" for="tracking-number">Nomor Pendaftaran / Tiket</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline" data-icon="tag">tag</span>
<input class="w-full pl-12 pr-4 py-4 rounded-lg border border-outline-variant bg-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container font-body-md text-body-md text-on-surface transition-colors" id="tracking-number" name="tracking_id" placeholder="Contoh: BPD-2024-00123" required="" type="text">
</div>
<div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-unit">
<a class="font-label-sm text-label-sm text-primary underline hover:text-primary-container transition-colors" href="#">Lupa nomor pendaftaran?</a>
<button class="w-full sm:w-auto bg-primary-container text-on-primary px-8 py-3 rounded-lg font-label-md text-label-md hover:bg-primary transition-colors flex items-center justify-center gap-2 shadow-sm" type="submit">
<span class="material-symbols-outlined text-[18px]" data-icon="manage_search">manage_search</span>
                            Cari Permohonan
                        </button>
</div>
</form>
</div>
</section>

<!-- FAQ Section -->
<section class="w-full max-w-4xl mx-auto mt-stack-lg pt-stack-lg border-t border-surface-container-high">
<div class="flex items-center gap-3 mb-stack-md">
<span class="material-symbols-outlined text-primary" data-icon="help" style="font-variation-settings: 'FILL' 1;">help</span>
<h2 class="font-headline-md text-headline-md text-primary">Bantuan &amp; FAQ</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
<div class="bg-surface-container-lowest p-stack-md rounded-lg border border-outline-variant hover:shadow-sm transition-shadow">
<h3 class="font-label-md text-label-md font-semibold text-on-surface mb-unit">Berapa lama proses permohonan?</h3>
<p class="font-body-md text-body-md text-on-surface-variant text-sm">Sesuai UU KIP, proses standar adalah 10 hari kerja, dan dapat diperpanjang 7 hari kerja dengan pemberitahuan.</p>
</div>
<div class="bg-surface-container-lowest p-stack-md rounded-lg border border-outline-variant hover:shadow-sm transition-shadow">
<h3 class="font-label-md text-label-md font-semibold text-on-surface mb-unit">Status menunjukkan "Ditangguhkan", apa maksudnya?</h3>
<p class="font-body-md text-body-md text-on-surface-variant text-sm">Informasi yang diminta mungkin memerlukan klarifikasi lebih lanjut. Silakan cek email Anda untuk detail dari petugas.</p>
</div>
</div>
</section>
</main>
@endsection
