<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>pinjam.in — Electronic Library</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #f8f7f1;
            --dark: #163b46;
            --teal: #22918b;
            --teal-dark: #176b69;
            --muted: #678087;
            --line: #dfe4df;
            --white: #ffffff;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--cream);
            color: var(--dark);
            font-family: "DM Sans", sans-serif;
            overflow-x: hidden;
        }

        /* ================= NAVBAR ================= */

        .navbar {
            width: 100%;
            padding: 17px 42px;
        }

        .nav-container {
            max-width: 1180px;
            height: 61px;
            margin: auto;

            display: flex;
            align-items: center;
            justify-content: space-between;

            border-bottom: 1px solid var(--line);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;

            text-decoration: none;
            color: var(--dark);
        }

        .brand-icon {
            width: 28px;
            height: 28px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 7px;

            background: var(--teal);
            color: white;

            font-size: 17px;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-text strong {
            font-size: 15px;
            line-height: 17px;
        }

        .brand-text span {
            color: var(--teal);
            font-size: 6px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .nav-menu {
            display: flex;
            gap: 34px;
            margin-left: 100px;
        }

        .nav-menu a,
        .login {
            color: #526a70;
            font-size: 13px;
            text-decoration: none;
            transition: .2s;
        }

        .nav-menu a:hover,
        .login:hover {
            color: var(--teal);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .btn-primary {
            padding: 13px 20px;

            border-radius: 7px;

            background: var(--teal);
            color: white;

            font-size: 13px;
            font-weight: 600;

            text-decoration: none;

            box-shadow: 0 10px 20px rgba(34, 145, 139, .16);

            transition: .2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: var(--teal-dark);
        }

        /* ================= HERO ================= */

        .hero {
            position: relative;
            min-height: 665px;
            padding-top: 90px;

            overflow: hidden;
        }

        .hero-container {
            width: 1180px;
            max-width: calc(100% - 84px);

            margin: auto;

            display: grid;
            grid-template-columns: 52% 48%;

            align-items: center;
        }

        .hero-content {
            position: relative;
            z-index: 3;
        }

        .eyebrow,
        .section-label {
            color: var(--teal-dark);

            font-size: 10px;
            font-weight: 700;

            letter-spacing: 1.6px;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 12px;

            margin-bottom: 23px;
        }

        .eyebrow span {
            width: 19px;
            height: 1px;
            background: var(--teal);
        }

        .hero h1 {
            max-width: 600px;

            font-family: "Playfair Display", serif;

            font-size: clamp(48px, 5vw, 68px);
            line-height: .99;
            letter-spacing: -3px;
            font-weight: 600;
        }

        .hero h1 span {
            color: var(--teal);
        }

        .hero-content > p {
            width: 450px;
            max-width: 100%;

            margin-top: 27px;

            color: #678087;

            font-size: 15px;
            line-height: 1.9;
        }

        .hero-buttons {
            display: flex;
            align-items: center;
            gap: 19px;

            margin-top: 28px;
        }

        .btn-hero {
            width: 235px;
            min-height: 54px;

            padding: 8px 17px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            background: var(--teal);
            color: white;

            border-radius: 7px;

            text-decoration: none;

            box-shadow: 0 12px 25px rgba(34, 145, 139, .18);

            transition: .2s;
        }

        .btn-hero:hover {
            transform: translateY(-2px);
        }

        .btn-hero span {
            text-align: center;
            width: 100%;

            font-size: 15px;
            line-height: 1.3;
            font-weight: 600;
        }

        .btn-hero b {
            font-size: 21px;
            font-weight: 400;
        }

        .how-link {
            display: flex;
            align-items: center;
            gap: 10px;

            color: var(--dark);

            text-decoration: none;

            font-size: 13px;
            font-weight: 600;
        }

        .how-link span {
            font-size: 25px;
            font-weight: 300;
        }

        /* ================= HERO VISUAL ================= */

        .hero-visual {
            height: 430px;
            position: relative;
        }

        .green-circle {
            position: absolute;

            width: 300px;
            height: 300px;

            top: 30px;
            right: 45px;

            border-radius: 50%;

            background: rgba(190, 219, 208, .45);
        }

        .orange-circle {
            position: absolute;

            width: 160px;
            height: 160px;

            right: 0;
            bottom: 35px;

            border-radius: 50%;

            background: rgba(242, 198, 174, .38);
        }

        .dotted-circle {
            position: absolute;

            width: 395px;
            height: 395px;

            top: 8px;
            right: 40px;

            border-radius: 50%;

            border: 1px dashed rgba(34, 145, 139, .35);
        }

        .dot {
            position: absolute;

            width: 10px;
            height: 10px;

            right: 193px;
            top: 0;

            border-radius: 50%;

            background: #ed9274;
        }

        /* ================= CARD ================= */

        .loan-card {
            position: absolute;

            width: 300px;
            height: 235px;

            top: 78px;
            right: 88px;

            padding: 32px 25px;

            background: white;

            border-radius: 14px;

            transform: rotate(3deg);

            box-shadow:
                0 25px 40px rgba(25, 62, 70, .10);

            z-index: 4;
        }

        .card-top {
            color: #789096;

            font-size: 9px;
            line-height: 1.5;
            letter-spacing: 1px;
        }

        .arrow-circle {
            position: absolute;

            right: 25px;
            top: 31px;

            width: 27px;
            height: 27px;

            border-radius: 50%;

            background: #e4f2eb;

            display: flex;
            align-items: center;
            justify-content: center;

            color: var(--teal-dark);

            font-size: 15px;
        }

        .price {
            margin-top: 24px;

            font-family: "Playfair Display", serif;

            font-size: 31px;
            font-weight: 600;
        }

        .description {
            margin-top: 7px;

            color: #789096;

            font-size: 10px;
        }

        .progress {
            width: 100%;
            height: 7px;

            margin-top: 26px;

            border-radius: 20px;

            background: #e9eee9;

            overflow: hidden;
        }

        .progress-bar {
            width: 68%;
            height: 100%;

            border-radius: inherit;

            background: var(--teal);
        }

        .progress-info {
            display: flex;
            justify-content: space-between;

            margin-top: 7px;

            color: #84969a;

            font-size: 9px;
        }

        .progress-info strong {
            font-weight: 700;
        }

        /* ================= BADGE ================= */

        .transparent-badge {
            position: absolute;

            left: 25px;
            bottom: 62px;

            width: 170px;
            height: 55px;

            display: flex;
            align-items: center;
            gap: 10px;

            padding: 0 17px;

            background: var(--dark);

            border-radius: 10px;

            transform: rotate(3deg);

            color: white;

            box-shadow: 0 14px 25px rgba(22, 59, 70, .15);

            z-index: 5;
        }

        .transparent-badge span {
            font-size: 10px;
            font-weight: 600;
        }

        .check {
            width: 15px;
            height: 15px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #78c4b2;
            border-radius: 50%;

            color: #78c4b2;

            font-size: 9px;
        }

        /* ================= BACKGROUND ================= */

        .circle-left {
            position: absolute;

            width: 460px;
            height: 460px;

            left: -160px;
            top: 170px;

            border-radius: 50%;

            border: 1px solid rgba(34, 145, 139, .13);

            box-shadow:
                0 0 0 35px rgba(34, 145, 139, .025),
                0 0 0 75px rgba(34, 145, 139, .02);
        }

        /* ================= BENEFIT ================= */

        .benefit-container {
            position: relative;
            z-index: 4;

            width: 1180px;
            max-width: calc(100% - 84px);

            margin: 0 auto;

            min-height: 73px;

            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .benefit-title {
            font-size: 13px;
            font-weight: 700;
        }

        .benefits {
            display: flex;
            gap: 37px;
        }

        .benefit {
            display: flex;
            align-items: center;
            gap: 8px;

            color: #82969b;

            font-size: 10px;
        }

        .benefit-icon {
            color: var(--teal);
            font-size: 17px;
        }

        /* ================= SECTIONS ================= */

        .about,
        .how-section,
        .faq {
            width: 1100px;
            max-width: calc(100% - 84px);

            margin: auto;

            padding: 110px 0;
        }

        .about h2,
        .how-section h2,
        .faq h2 {
            margin-top: 20px;

            font-family: "Playfair Display", serif;

            font-size: 47px;
            line-height: 1.1;
        }

        .about h2 span,
        .how-section h2 span,
        .faq h2 span {
            color: var(--teal);
        }

        .about > p {
            width: 520px;
            max-width: 100%;

            margin-top: 25px;

            color: var(--muted);

            line-height: 1.8;
        }

        /* ================= STEPS ================= */

        .how-section {
            border-top: 1px solid var(--line);
        }

        .steps {
            margin-top: 60px;

            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .step {
            padding: 25px;

            border: 1px solid var(--line);
            border-radius: 12px;

            background: rgba(255,255,255,.3);
        }

        .step-number {
            color: var(--teal);

            font-size: 12px;
            font-weight: 700;
        }

        .step h3 {
            margin-top: 25px;

            font-size: 17px;
        }

        .step p {
            margin-top: 12px;

            color: var(--muted);

            font-size: 13px;
            line-height: 1.7;
        }

        /* ================= FAQ ================= */

        .faq {
            border-top: 1px solid var(--line);
        }

        .faq-list {
            margin-top: 45px;
        }

        details {
            padding: 22px 0;

            border-bottom: 1px solid var(--line);
        }

        summary {
            cursor: pointer;

            display: flex;
            justify-content: space-between;

            list-style: none;

            font-weight: 600;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        summary span {
            color: var(--teal);
            font-size: 22px;
        }

        details p {
            margin-top: 15px;

            max-width: 650px;

            color: var(--muted);

            line-height: 1.7;
        }

        /* ================= FOOTER ================= */

        footer {
            padding: 35px 42px;

            border-top: 1px solid var(--line);

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
        }

        .footer-brand strong {
            font-size: 15px;
        }

        .footer-brand span {
            color: var(--teal);

            font-size: 6px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        footer p {
            color: #82969b;
            font-size: 11px;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {

            .navbar {
                padding: 12px 20px;
            }

            .nav-menu {
                display: none;
            }

            .nav-right {
                gap: 10px;
            }

            .login {
                display: none;
            }

            .hero {
                padding-top: 60px;
            }

            .hero-container {
                grid-template-columns: 1fr;
            }

            .hero-visual {
                margin-top: 60px;
            }

            .benefit-container {
                margin-top: 40px;
            }

            .benefits {
                gap: 15px;
            }

            .steps {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {

            .hero-container,
            .benefit-container {
                max-width: calc(100% - 40px);
            }

            .hero h1 {
                font-size: 46px;
                letter-spacing: -2px;
            }

            .hero-content > p {
                font-size: 14px;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-visual {
                transform: scale(.8);
                transform-origin: top left;
                width: 125%;
            }

            .benefit-container {
                display: block;
                padding: 20px 0;
            }

            .benefits {
                margin-top: 20px;
                flex-direction: column;
            }

            .about,
            .how-section,
            .faq {
                max-width: calc(100% - 40px);
                padding: 75px 0;
            }

            .about h2,
            .how-section h2,
            .faq h2 {
                font-size: 38px;
            }

            .steps {
                grid-template-columns: 1fr;
            }

            footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- ================= NAVBAR ================= -->

    <header class="navbar">

        <div class="nav-container">

            <a href="#" class="brand">

                <div class="brand-icon">
                    ✣
                </div>

                <div class="brand-text">
                    <strong>pinjam.in</strong>
                    <span>ELECTRONIC LIBRARY</span>
                </div>

            </a>

            <div class="nav-right">

                <a href="View/v_register.php" class="login">
                    Register
                </a>

                <a href="View/v_login.php" class="btn-primary">
                    Login
                </a>

            </div>

        </div>

    </header>


    <!-- ================= HERO ================= -->

    <main>

        <section class="hero">

            <!-- Lingkaran background -->
            <div class="circle-left"></div>


            <div class="hero-container">

                <!-- LEFT -->

                <div class="hero-content">

                    <div class="eyebrow">

                        <span></span>

                        KOPERASI DIGITAL YANG DEKAT

                    </div>


                    <h1>

                        Rencana baik,

                        <br>

                        <span>
                            dimulai dari sini.
                        </span>

                    </h1>


                    <p>

                        Peminjaman alat elektronik yang mudah,
                        proses yang jelas, dan dukungan nyata
                        untuk kebutuhan Anda.

                    </p>




                <!-- RIGHT -->

                <div class="hero-visual">

                    <div class="dotted-circle"></div>

                    <div class="green-circle"></div>

                    <div class="orange-circle"></div>

                    <div class="dot"></div>


                    <!-- CARD -->

                    <div class="loan-card">

                        <div class="card-top">

                            <span>
                                ALAT
                            </span>

                            <br>

                            <span>
                                TERSEDIA
                            </span>

                        </div>


                        <div class="arrow-circle">
                            →
                        </div>


                        <div class="price">
                            Laptop ASUS
                        </div>


                        <div class="description">
                            Peminjaman · 3 hari
                        </div>


                        <div class="progress">

                            <div class="progress-bar"></div>

                        </div>


                        <div class="progress-info">

                            <span>
                                Ketersediaan
                            </span>

                            <strong>
                                68%
                            </strong>

                        </div>

                    </div>


                    <!-- BADGE -->

                    <div class="transparent-badge">

                        <div class="check">
                            ✓
                        </div>

                        <span>
                            Pengajuan transparan
                        </span>

                    </div>

                </div>

            </div>


            <!-- BENEFITS -->

    

            </div>

        </section>


        <!-- ================= TENTANG ================= -->

        <section class="about" id="tentang">

            <div class="section-label">
                MENGAPA PINJAM.IN
            </div>


            <h2>

                Kebutuhan alat,

                <br>

                <span>
                    lebih mudah dipenuhi.
                </span>

            </h2>


            <p>

                pinjam.in membantu proses peminjaman alat
                elektronik menjadi lebih sederhana,
                transparan, dan teratur.

            </p>

        </section>


        <!-- ================= CARA KERJA ================= -->

        <section class="how-section" id="cara-kerja">

            <div class="section-label">
                CARA KERJA
            </div>


            <h2>

                Peminjaman dalam

                <span>
                    beberapa langkah.
                </span>

            </h2>


            <div class="steps">

                <div class="step">

                    <div class="step-number">
                        01
                    </div>

                    <h3>
                        Pilih alat
                    </h3>

                    <p>
                        Lihat alat elektronik yang tersedia
                        dan pilih sesuai kebutuhan.
                    </p>

                </div>


                <div class="step">

                    <div class="step-number">
                        02
                    </div>

                    <h3>
                        Ajukan peminjaman
                    </h3>

                    <p>
                        Isi data pengajuan dan tentukan
                        alat yang ingin dipinjam.
                    </p>

                </div>


                <div class="step">

                    <div class="step-number">
                        03
                    </div>

                    <h3>
                        Ambil alat
                    </h3>

                    <p>
                        Setelah disetujui, alat dapat
                        diambil sesuai ketentuan.
                    </p>

                </div>


                <div class="step">

                    <div class="step-number">
                        04
                    </div>

                    <h3>
                        Kembalikan
                    </h3>

                    <p>
                        Kembalikan alat sesuai jadwal
                        dan kondisi yang ditentukan.
                    </p>

                </div>

            </div>

        </section>

    </main>


    <!-- ================= FOOTER ================= -->

    <footer>

        <div class="footer-brand">

            <strong>
                pinjam.in
            </strong>

            <span>
                ELECTRONIC LIBRARY
            </span>

        </div>



    </footer>


    <!-- ================= JAVASCRIPT ================= -->

    <script>

        // Smooth scroll

        document
            .querySelectorAll('a[href^="#"]')
            .forEach(link => {

                link.addEventListener("click", function(e) {

                    const target =
                        document.querySelector(
                            this.getAttribute("href")
                        );

                    if (target) {

                        e.preventDefault();

                        target.scrollIntoView({
                            behavior: "smooth"
                        });

                    }

                });

            });


        // Efek tombol

        document
            .querySelectorAll(".btn-primary, .btn-hero")
            .forEach(button => {

                button.addEventListener("click", function() {

                    console.log(
                        "Tombol pengajuan diklik"
                    );

                });

            });

    </script>

</body>
</html>