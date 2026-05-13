<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHY-FA-CAT</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/icon-cat.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/icon-cat.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/icon-cat.png') ?>">

    


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">



    <!-- Custom CSS -->
    <style>
        .hero-section {
            background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
            padding: 100px 0;
            color: white;
        }

        .hero-section .text-section {
            z-index: 1;
        }

        .hero-image {
            max-width: 80%;
            height: auto;
            border-radius: 10px;
        }

        .judul-hero {
            font-weight: bold;
        }

        .hero-section .btn-primary {
            background-color: #FFFFFF;
            color: #481F64;
            border: 2px solid #FFFFFF;
            transition: all 0.3s ease;
        }

        .hero-section .btn-primary:hover {
            background-color: rgba(255, 255, 255, 0.9);
            color: #17376E;
            transform: scale(1.05);
        }

        .hero-section .btn-outline-primary {
            background-color: transparent;
            color: #FFFFFF;
            border: 2px solid #FFFFFF;
            transition: all 0.3s ease;
        }

        .hero-section .btn-outline-primary:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            transform: scale(1.05);
        }

        .title-hero {
            font-weight: bold;
        }


        @media (max-width: 768px) {
            .hero-section {
                padding: 50px 0;
                text-align: center;
            }

            .hero-section .row {
                flex-direction: column-reverse;
            }

            .hero-image-section {
                margin-bottom: 30px;
            }

            .hero-buttons {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .hero-buttons .btn {
                width: 200px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <?= $this->include('templates/navbar') ?>

    <?= $this->renderSection('content') ?>

    <?= $this->include('templates/footer') ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

</body>

</html>