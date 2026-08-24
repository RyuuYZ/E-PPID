@extends('layouts.public')

@section('content')
<main class="flex-grow flex flex-col items-center w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
<!-- Hero Section -->
<section class="w-full py-16 md:py-24 flex flex-col items-center text-center gap-stack-lg border-b border-surface-variant">
<h1 class="font-display-lg text-display-lg text-primary max-w-4xl tracking-tight">Ajukan &amp; Pantau Permohonan Informasi Publik</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">Layanan informasi publik yang transparan, akuntabel, dan mudah diakses. Kami berkomitmen menyediakan informasi pemerintahan daerah secara cepat dan tepat saji.</p>
<div class="flex flex-col sm:flex-row gap-4 mt-8 w-full sm:w-auto">
<a href="{{ route('permohonan.create') }}" class="bg-primary-container text-on-primary font-label-md text-label-md px-8 py-4 rounded-xl shadow-sm hover:shadow-md hover:bg-primary transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined">add_circle</span>
                    Ajukan Permohonan Baru
                </a>
<a href="{{ route('permohonan.lacak') }}" class="bg-surface border border-primary-container text-primary-container font-label-md text-label-md px-8 py-4 rounded-xl hover:bg-surface-container-low transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined">search</span>
                    Lacak Status Permohonan
                </a>
</div>
</section>
<!-- Categories Section -->
<section class="w-full py-16 flex flex-col gap-stack-lg">
<h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary text-center">Kategori Informasi Publik</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter mt-8">
<!-- Card 1 -->
<div class="bg-surface-container-lowest border border-surface-variant p-6 rounded-2xl flex flex-col items-center text-center gap-4 hover:shadow-[0_4px_20px_rgba(31,56,100,0.08)] hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
<div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary-container group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[32px]">calendar_month</span>
</div>
<h3 class="font-headline-md text-headline-md text-primary">Informasi Berkala</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Informasi yang wajib disediakan dan diumumkan secara berkala, minimal 6 bulan sekali.</p>
</div>
<!-- Card 2 -->
<div class="bg-surface-container-lowest border border-surface-variant p-6 rounded-2xl flex flex-col items-center text-center gap-4 hover:shadow-[0_4px_20px_rgba(31,56,100,0.08)] hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
<div class="w-16 h-16 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[32px]">campaign</span>
</div>
<h3 class="font-headline-md text-headline-md text-primary">Serta Merta</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Informasi publik yang dapat mengancam hajat hidup orang banyak dan ketertiban umum.</p>
</div>
<!-- Card 3 -->
<div class="bg-surface-container-lowest border border-surface-variant p-6 rounded-2xl flex flex-col items-center text-center gap-4 hover:shadow-[0_4px_20px_rgba(31,56,100,0.08)] hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
<div class="w-16 h-16 rounded-full bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[32px]">schedule</span>
</div>
<h3 class="font-headline-md text-headline-md text-primary">Setiap Saat</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Informasi yang harus disediakan oleh Badan Publik dan siap tersedia untuk bisa langsung diberikan.</p>
</div>
<!-- Card 4 -->
<div class="bg-surface-container-lowest border border-surface-variant p-6 rounded-2xl flex flex-col items-center text-center gap-4 hover:shadow-[0_4px_20px_rgba(31,56,100,0.08)] hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
<div class="w-16 h-16 rounded-full bg-error-container flex items-center justify-center text-on-error-container group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[32px]">gavel</span>
</div>
<h3 class="font-headline-md text-headline-md text-primary">Keberatan</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Prosedur pengajuan keberatan jika layanan informasi tidak sesuai dengan ketentuan perundang-undangan.</p>
</div>
</div>
</section>
</main>
@endsection
