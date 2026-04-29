<?php
// =========================================================
// LOGIKA WHATSAPP UNIVERSAL (DIPINDAHKAN KE FOOTER)
// Ini memastikan tombol WA berfungsi di semua halaman.
// =========================================================

// 1. Tentukan Nomor WA Default (Cadangan)
 $wa_number_default = '6282391856461'; // Ganti dengan nomor cadangan jika DB error

// 2. Cek apakah data profil dari database tersedia
 $wa_number = $wa_number_default;
if (isset($profile) && !empty($profile['phone'])) {
    // Bersihkan nomor dari karakter aneh (spasi, +, -)
    $wa_number = preg_replace('/[^0-9]/', '', $profile['phone']);
}

// 3. Buat Format Pesan
 $wa_text  = "Untuk pendaftaran WA silakan ketik%0A%0A";
 $wa_text .= "Hari %26 Tanggal berobat : %0A";
 $wa_text .= "Nama lengkap : %0A";
 $wa_text .= "Tanggal lahir : %0A";
 $wa_text .= "Alamat lengkap : %0A";
 $wa_text .= "No hp : %0A";
 $wa_text .= "Poli tujuan : %0A";
 $wa_text .= "Nama Dokter : %0A";
 $wa_text .= "Jenis Pembayaran : %0A";
 $wa_text .= "No. KTP : %0A%0A";
 $wa_text .= "Terimakasih 🙏";

// 4. Gabungkan menjadi Link Lengkap
 $wa_link = "https://wa.me/" . $wa_number . "?text=" . $wa_text;
?>

<!-- ================================================== -->
<!-- TOMBOL WHATSAPP & BACK TO TOP -->
<!-- ================================================== -->

<!-- Tombol Back to Top -->
<a href="#" class="back-to-top d-none" style="display: none;">
    <i class="fas fa-chevron-up"></i>
</a>

<!-- Tombol WhatsApp -->
<a href="<?php echo $wa_link; ?>" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Jangan lupa menutup tag body dan html jika belum ditutup di footer ini -->
<!-- </body>
</html> -->
        
        <!-- Footer Start -->
        <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.1s" id="contact">
            <div class="container py-5">
                <div class="row g-5">
                    <!-- Kolom 1: Tentang RSIA -->
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-item">
                            <h2 class="fw-bold mb-3"><span class="text-primary mb-0">RSIA</span> <span class="text-secondary">Restu Ibu</span></h2>
                            <p class="mb-4">RSIA Restu Ibu Padang berkomitmen memberikan pelayanan kesehatan terbaik bagi Ibu dan Anak dengan sentuhan kasih sayang.</p>
                            <div class="d-flex">
                                <a class="btn btn-primary btn-sm-square rounded-circle me-2" href="https://www.tiktok.com/@rsiarestuibu" target="_blank"><i class="fab fa-tiktok text-white"></i></a>
                                <a class="btn btn-primary btn-sm-square rounded-circle me-2" href="https://www.instagram.com/rsiarestuibu/" target="_blank"><i class="fab fa-instagram text-white"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2: Quick Links -->
                    <div class="col-lg-2 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">Pintasan</h4>
                            <div class="d-flex flex-column align-items-start">
                                <a href="index" class="text-body mb-3"><i class="fas fa-angle-right me-2 text-primary"></i> Beranda</a>
                                <a href="dokter" class="text-body mb-3"><i class="fas fa-angle-right me-2 text-primary"></i> Dokter Praktek</a>
                                <a href="berita" class="text-body mb-3"><i class="fas fa-angle-right me-2 text-primary"></i> Berita</a>
                                <!--<a href="index#contact" class="text-body"><i class="fas fa-angle-right me-2 text-primary"></i> Kontak</a>-->
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 3: Kontak Info -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">Hubungi Kami</h4>
                            <div class="d-flex flex-column align-items-start">
                                <div class="d-flex mb-3">
                                    <i class="fas fa-map-marker-alt text-primary fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Alamat</h6>
                                        <small class="text-muted">Jl. Terandam No.5, RW.7, Sawahan, Kec. Padang Tim., Kota Padang, Sumatera Barat 25133</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <i class="fas fa-phone-alt text-primary fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Telepon</h6>
                                        <small class="text-muted">(0751) 810756</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 4: Google Maps -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius">Lokasi Kami</h4>
                            <div class="map-container rounded overflow-hidden border border-primary shadow-sm">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d997.317473909205!2d100.36666556339215!3d-0.9500020679561254!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4b938b73ce47b%3A0xe8c52430e2f7636b!2sRumah%20Sakit%20Ibu%20dan%20Anak%20Restu%20Ibu!5e0!3m2!1sid!2sid!4v1772526313992!5m2!1sid!2sid" width="100%" height="200" style="border:0; filter: grayscale(20%) contrast(1.1);" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>RSIA Restu Ibu</a>, All right reserved.</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->

        <!-- Tombol Back to Top -->
        <a href="#" class="back-to-top" style="display: none;">
            <i class="fas fa-chevron-up"></i> <!-- Icon Panah Atas yang keren -->
        </a>   

        <!-- WhatsApp Float Button -->
        <!--<a href="https://wa.me/6282391856461" class="whatsapp-float" target="_blank" rel="noopener noreferrer">-->
        <!--    <i class="fab fa-whatsapp"></i>-->
        <!--</a>-->
        <!-- TOMBOL WHATSAPP (Di footer atau index) -->
        <a href="<?php echo $wa_link; ?>" class="whatsapp-float" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>

        
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/lightbox/js/lightbox.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>

        <!-- Schema Markup untuk SEO Lokal -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Hospital",
          "name": "RSIA Restu Ibu",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jl. Terandam No.5, RW.7, Sawahan",
            "addressLocality": "Padang",
            "addressRegion": "Sumatera Barat",
            "postalCode": "25121",
            "addressCountry": "ID"
          },
          "telephone": "<?php echo $profile['phone'] ?? ''; ?>",
          "openingHours": "Mo-Su 00:00-24:00",
          "image": "https://official.rsiarestuibu.my.id/img/<?php echo $adminLogo; ?>"
        }
        </script>

        </body>

        </html>