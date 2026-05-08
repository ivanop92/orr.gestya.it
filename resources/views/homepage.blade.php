<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestya - Soluzioni di Gestione Aziendale</title>
    <link rel="shortcut icon" href="/icona.png">

    <!-- Meta tag per la condivisione su social e WhatsApp -->
    <meta property="og:title" content="Gestya - Gestione aziendale semplificata">
    <meta property="og:description" content="La piattaforma all-in-one che ti aiuta a gestire ogni aspetto della tua azienda con facilità ed efficienza. Prenota una demo gratuita!">
    <meta property="og:image" content="https://gestya.it/logo_gestya.jpg">
    <meta property="og:url" content="https://gestya.it">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .hero-section {
            background-color: #f8fafc;
            background-image: url('https://via.placeholder.com/1920x1080');
            background-size: cover;
            background-position: center;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #2563eb;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
        }
        .calendly-inline-widget {
            min-width: 320px;
            height: 630px;
        }
    </style>
</head>
<body class="font-sans">
<!-- Header -->
<header class="bg-white shadow-sm fixed w-full z-10">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center">
            <a href="#" class="text-2xl font-bold text-blue-600">
                <img src="/logo_gestya.jpg" style="width:200px" alt="Gestya">
            </a>
        </div>
        <nav class="hidden md:flex space-x-8">
            <a href="#features" class="text-gray-600 hover:text-blue-600 transition">Funzionalità</a>
            <a href="#specific-solutions" class="text-gray-600 hover:text-blue-600 transition">Soluzioni specifiche</a>
            <a href="#benefits" class="text-gray-600 hover:text-blue-600 transition">Vantaggi</a>
            <a href="#demo" class="text-gray-600 hover:text-blue-600 transition">Prenota Demo</a>
        </nav>
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
        <div class="container mx-auto px-4 py-2 space-y-2">
            <a href="#features" class="block text-gray-600 hover:text-blue-600 transition">Funzionalità</a>
            <a href="#specific-solutions" class="block text-gray-600 hover:text-blue-600 transition">Soluzioni specifiche</a>
            <a href="#benefits" class="block text-gray-600 hover:text-blue-600 transition">Vantaggi</a>
            <a href="#demo" class="block text-gray-600 hover:text-blue-600 transition">Prenota Demo</a>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="hero-section pt-32 pb-16">
    <div class="container mx-auto px-4 flex flex-col items-center text-center">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 mb-6">Gestione aziendale semplificata con Gestya</h1>
        <p class="text-xl text-gray-600 max-w-3xl mb-8">La piattaforma all-in-one che ti aiuta a gestire ogni aspetto della tua azienda con facilità ed efficienza.</p>

        <!-- YouTube Video Embed -->
        <div class="w-full max-w-4xl mb-10 rounded-lg shadow-xl overflow-hidden">
            <div class="relative" style="padding-bottom: 56.25%;">

                <iframe
                        class="absolute top-0 left-0 w-full h-full"
                        src="https://www.youtube.com/embed/PaCOzqL07GA?autoplay=1"
                        title="Gestya - Video di presentazione"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen allow="autoplay">
                </iframe>
            </div>
        </div>

        <a href="#demo" class="btn-primary text-white px-8 py-4 rounded-lg font-medium text-lg shadow-lg">Prenota una Demo Gratuita</a>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Le nostre funzionalità</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                <div class="text-blue-500 mb-4">
                    <i class="fas fa-chart-line text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Analisi avanzate</h3>
                <p class="text-gray-600">Dashboard personalizzabili con report dettagliati per tenere traccia delle performance aziendali in tempo reale.</p>
            </div>
            <!-- Feature 2 -->
            <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                <div class="text-blue-500 mb-4">
                    <i class="fas fa-users text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Gestione clienti</h3>
                <p class="text-gray-600">Gestisci contatti, attività e opportunità di vendita in un unico posto per migliorare le relazioni con i clienti.</p>
            </div>
            <!-- Feature 3 -->
            <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                <div class="text-blue-500 mb-4">
                    <i class="fas fa-tasks text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Project management</h3>
                <p class="text-gray-600">Organizza, pianifica e monitora i progetti con strumenti collaborativi che aumentano la produttività del team.</p>
            </div>
            <!-- Feature 4 -->
            <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                <div class="text-blue-500 mb-4">
                    <i class="fas fa-file-invoice text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Fatturazione automatizzata</h3>
                <p class="text-gray-600">Crea e invia fatture in pochi clic, monitora i pagamenti e gestisci la contabilità senza stress.</p>
            </div>
            <!-- Feature 5 -->
            <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                <div class="text-blue-500 mb-4">
                    <i class="fas fa-clipboard-list text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Inventario e fornitori</h3>
                <p class="text-gray-600">Tieni sotto controllo scorte, ordini e relazioni con i fornitori per ottimizzare la catena di approvvigionamento.</p>
            </div>
            <!-- Feature 6 -->
            <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                <div class="text-blue-500 mb-4">
                    <i class="fas fa-mobile-alt text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">App mobile</h3>
                <p class="text-gray-600">Accedi a tutte le funzionalità anche in movimento tramite l'app mobile disponibile per iOS e Android.</p>
            </div>
        </div>
    </div>
</section>

<!-- Nuova sezione: Soluzioni specifiche -->
<section id="specific-solutions" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Soluzioni specifiche per il tuo business</h2>

        <!-- Gestione Magazzino -->
        <div class="mb-16 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 items-center">
                <div class="p-8">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-barcode text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Gestione del magazzino con lettori barcode</h3>
                    <p class="text-gray-600 mb-6">Ottimizza la gestione del magazzino con un sistema completo che integra lettori di codici a barre per un controllo preciso delle scorte in tempo reale.</p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Tracciamento in tempo reale dei movimenti di magazzino con lettori barcode wireless</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Inventario automatizzato con minimizzazione degli errori manuali</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Gestione della posizione degli articoli per ottimizzare lo spazio di stoccaggio</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Alerting automatico per scorte minime e riordini</p>
                        </li>
                    </ul>
                </div>
                <div class="bg-blue-50 p-8 h-full flex items-center justify-center">
                    <img style="height:500px;" src="https://5.imimg.com/data5/SELLER/Default/2024/12/470439087/YT/RG/AU/3553560/android-barcode-scanner-1000x1000.png" alt="Gestione magazzino con barcode" class="rounded-lg shadow-md max-w-full" onerror="this.src='/api/placeholder/600/400'; this.onerror=null;">
                </div>
            </div>
        </div>

        <!-- Gestione Agenti di Vendita -->
        <div class="mb-16 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 items-center">
                <div class="bg-blue-50 p-8 h-full flex items-center justify-center md:order-first">
                    <img src="https://www.weblink.it/wp-content/uploads/2024/10/app-per-agenti-1.jpg" alt="Gestione agenti di vendita" class="rounded-lg shadow-md max-w-full" onerror="this.src='/api/placeholder/600/400'; this.onerror=null;">
                </div>
                <div class="p-8">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-handshake text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Gestione agenti di vendita con provvigioni</h3>
                    <p class="text-gray-600 mb-6">Sistema avanzato per gestire la tua rete commerciale, monitorare le performance e calcolare automaticamente le provvigioni.</p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Calcolo automatico delle provvigioni con differenti modelli personalizzabili</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Dashboard individuali per ogni agente con obiettivi e risultati</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Gestione zone di competenza con mappatura geografica</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Reportistica dettagliata sulle performance di vendita</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Gestione Produzione -->
        <div class="mb-16 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 items-center">
                <div class="p-8">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-industry text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Gestione produzione con avanzamenti delle fasi</h3>
                    <p class="text-gray-600 mb-6">Monitora e ottimizza il processo produttivo con un sistema integrato per il controllo delle fasi di lavorazione tramite dispositivi touchscreen.</p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Monitoraggio in tempo reale degli avanzamenti di produzione tramite tablet o totem</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Gestione delle distinte base e pianificazione della produzione</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Tracciamento dei tempi di lavorazione e calcolo automatico dell'efficienza</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Interfaccia semplificata per operatori di linea con accesso tramite QR code</p>
                        </li>
                    </ul>
                </div>
                <div class="bg-blue-50 p-8 h-full flex items-center justify-center">
                    <img src="https://www.gantt.com/img/gantt-chart-1.jpg" alt="Gestione produzione" class="rounded-lg shadow-md max-w-full" onerror="this.src='/api/placeholder/600/400'; this.onerror=null;">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 items-center">
                <div class="bg-blue-50 p-8 h-full flex items-center justify-center md:order-first">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/JyorVebQep0?si=hCymlmXuWWFLD9es" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>                </div>
                <div class="p-8">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-robot text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Integrazione MES Industria 4.0</h3>
                    <p class="text-gray-600 mb-6">Trasforma la tua produzione con soluzioni MES (Manufacturing Execution System) completamente integrate nell'ecosistema Industria 4.0.</p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Connessione diretta con macchinari e sistemi di automazione industriale</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Raccolta e analisi dati in tempo reale per ottimizzare la produzione</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Manutenzione predittiva basata su intelligenza artificiale</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Conformità totale ai requisiti normativi per gli incentivi fiscali Industria 4.0</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 text-green-500">
                                <i class="fas fa-check-circle mt-1"></i>
                            </div>
                            <p class="ml-3 text-gray-600">Dashboard personalizzabili con KPI di produzione in tempo reale</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


    </div>
</section>




<!-- Benefits Section -->
<section id="benefits" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Perché scegliere Gestya</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div>
                <ul class="space-y-6">
                    <li class="flex items-start">
                        <div class="flex-shrink-0 text-green-500">
                            <i class="fas fa-check-circle text-xl mt-1"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold mb-2 text-gray-800">Risparmio di tempo</h3>
                            <p class="text-gray-600">Automatizza i processi ripetitivi e riduci il tempo dedicato alle attività amministrative fino al 70%.</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 text-green-500">
                            <i class="fas fa-check-circle text-xl mt-1"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold mb-2 text-gray-800">Facilità d'uso</h3>
                            <p class="text-gray-600">Interface intuitiva che non richiede competenze tecniche avanzate, adatta a team di qualsiasi dimensione.</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 text-green-500">
                            <i class="fas fa-check-circle text-xl mt-1"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold mb-2 text-gray-800">Supporto dedicato</h3>
                            <p class="text-gray-600">Team di assistenza disponibile 7 giorni su 7 per risolvere qualsiasi problema o dubbio.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div>
                <img src="/gestya_dashboard.png" alt="Gestya Dashboard" class="rounded-lg shadow-xl">
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Cosa dicono i nostri clienti</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-gray-50 p-6 rounded-lg shadow">
                <div class="text-yellow-400 flex mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 mb-6 italic">"Da quando abbiamo implementato Gestya, la nostra produttività è aumentata del 35% e abbiamo ridotto drasticamente gli errori amministrativi."</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        MR
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Marco Rossi</h4>
                        <p class="text-gray-500 text-sm">CEO, TechSolutions</p>
                    </div>
                </div>
            </div>
            <!-- Testimonial 2 -->
            <div class="bg-gray-50 p-6 rounded-lg shadow">
                <div class="text-yellow-400 flex mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 mb-6 italic">"L'integrazione di tutti i dati aziendali in un'unica piattaforma ci ha permesso di prendere decisioni più rapide e informate. Servizio eccellente!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        LB
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Laura Bianchi</h4>
                        <p class="text-gray-500 text-sm">COO, Retail Express</p>
                    </div>
                </div>
            </div>
            <!-- Testimonial 3 -->
            <div class="bg-gray-50 p-6 rounded-lg shadow">
                <div class="text-yellow-400 flex mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="text-gray-600 mb-6 italic">"Come piccola impresa, avevamo bisogno di una soluzione che crescesse con noi. Gestya ci ha fornito tutti gli strumenti necessari a un prezzo accessibile."</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        GV
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-800">Giuseppe Verdi</h4>
                        <p class="text-gray-500 text-sm">Fondatore, Artigiani Mobili</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Demo Booking Section (Calendly Integration) -->
<section id="demo" class="py-16 bg-blue-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Prenota una demo gratuita</h2>
            <p class="text-center text-gray-600 max-w-2xl mx-auto">Scopri come Gestya può aiutare la tua azienda a crescere. Seleziona uno slot disponibile nel nostro calendario per una dimostrazione personalizzata.</p>

            <!-- Calendly inline widget -->
            <script>!function(window){const host="https://labs.heygen.com",url=host+"/guest/streaming-embed?share=eyJxdWFsaXR5IjoiaGlnaCIsImF2YXRhck5hbWUiOiJLYXR5YV9CbGFja19TdWl0X3B1YmxpYyIs%0D%0AInByZXZpZXdJbWciOiJodHRwczovL2ZpbGVzMi5oZXlnZW4uYWkvYXZhdGFyL3YzL2RhNWNiYTZi%0D%0AYzdiMzRjNWVhMTM5Zjc3ZGE5OGZkYzA0XzU1MzcwL3ByZXZpZXdfdGFsa18xLndlYnAiLCJuZWVk%0D%0AUmVtb3ZlQmFja2dyb3VuZCI6dHJ1ZSwia25vd2xlZGdlQmFzZUlkIjoiYmNjNTdmYmY5YjVjNDI1%0D%0ANzk0ODUxYmQ4ZDM3ZDdmZjEiLCJ1c2VybmFtZSI6IjQ3OTM3ZTIwMzNmMjRiNzZhYzk1ODk3NjNj%0D%0AOGM3YWY1In0%3D&inIFrame=1",clientWidth=document.body.clientWidth,wrapDiv=document.createElement("div");wrapDiv.id="heygen-streaming-embed";const container=document.createElement("div");container.id="heygen-streaming-container";const stylesheet=document.createElement("style");stylesheet.innerHTML=`\n  #heygen-streaming-embed {\n    z-index: 9999;\n    position: fixed;\n    left: 40px;\n    bottom: 40px;\n    width: 200px;\n    height: 200px;\n    border-radius: 50%;\n    border: 2px solid #fff;\n    box-shadow: 0px 8px 24px 0px rgba(0, 0, 0, 0.12);\n    transition: all linear 0.1s;\n    overflow: hidden;\n\n    opacity: 0;\n    visibility: hidden;\n  }\n  #heygen-streaming-embed.show {\n    opacity: 1;\n    visibility: visible;\n  }\n  #heygen-streaming-embed.expand {\n    ${clientWidth<540?"height: 266px; width: 96%; left: 50%; transform: translateX(-50%);":"height: 366px; width: calc(366px * 16 / 9);"}\n    border: 0;\n    border-radius: 8px;\n  }\n  #heygen-streaming-container {\n    width: 100%;\n    height: 100%;\n  }\n  #heygen-streaming-container iframe {\n    width: 100%;\n    height: 100%;\n    border: 0;\n  }\n  `;const iframe=document.createElement("iframe");iframe.allowFullscreen=!1,iframe.title="Streaming Embed",iframe.role="dialog",iframe.allow="microphone",iframe.src=url;let visible=!1,initial=!1;window.addEventListener("message",(e=>{e.origin===host&&e.data&&e.data.type&&"streaming-embed"===e.data.type&&("init"===e.data.action?(initial=!0,wrapDiv.classList.toggle("show",initial)):"show"===e.data.action?(visible=!0,wrapDiv.classList.toggle("expand",visible)):"hide"===e.data.action&&(visible=!1,wrapDiv.classList.toggle("expand",visible)))})),container.appendChild(iframe),wrapDiv.appendChild(stylesheet),wrapDiv.appendChild(container),document.body.appendChild(wrapDiv)}(globalThis);</script>
            <!-- Calendly inline widget begin -->
            <div class="calendly-inline-widget" data-url="https://calendly.com/ufficiotecnico-ingenia/demo-gestya-it" style="min-width:320px;height:1000px;"></div>
            <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
            <!-- Calendly inline widget end -->

            <!-- Calendly inline widget end -->
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 bg-blue-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-6">Pronto a trasformare la tua azienda?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Unisciti a centinaia di aziende che ogni giorno utilizzano Gestya per migliorare l'efficienza e aumentare i profitti.</p>
        <a href="#demo" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-medium text-lg shadow-lg hover:bg-gray-100 transition duration-300">Prenota la tua demo ora</a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 text-gray-300 py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4 text-white">Gestya</h3>
                <p class="mb-4">Soluzioni innovative per la gestione aziendale</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4 text-white">Collegamenti rapidi</h4>
                <ul class="space-y-2">
                    <li><a href="#features" class="hover:text-white transition">Funzionalità</a></li>
                    <li><a href="#benefits" class="hover:text-white transition">Vantaggi</a></li>
                    <li><a href="#demo" class="hover:text-white transition">Prenota Demo</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4 text-white">Risorse</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">Blog</a></li>
                    <li><a href="#" class="hover:text-white transition">Guide</a></li>
                    <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="#" class="hover:text-white transition">Supporto</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4 text-white">Contatti</h4>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                        <span>Via Roma 123, Milano, Italia</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-2"></i>
                        <span>info@gestya.it</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-2"></i>
                        <span>+39 02 1234567</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p>&copy; 2025 Ingenia SRL, via Carlo Del Balzo 17, Avellino. Tutti i diritti riservati.</p>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Account for fixed header
                    behavior: 'smooth'
                });

                // Close mobile menu if open
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    });
</script>
</body>
</html>