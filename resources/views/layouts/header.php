
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Impulso Fitness</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS BASE DEL SISTEMA -->
    <link href="/Impulso_Fitness/public/css/base.css" rel="stylesheet">

    <!-- LAYOUT COMPARTIDO -->
    <link href="/Impulso_Fitness/public/css/layout.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

     <!-- CSS DEL MÓDULO -->
      <?php if (!empty($extraCss)): ?>
        <link href="<?= $extraCss ?>" rel="stylesheet">
     <?php endif; ?>
     
</head>