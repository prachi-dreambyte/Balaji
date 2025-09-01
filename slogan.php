  <style>
        .slogan-section {
            padding: 20px 0;
            text-align: center;
            position: relative;
            background-color: #F5F6F2;
            overflow: hidden;
        }

        .slogan-text {
            font-size: 42px;
            font-weight: 700;
            line-height: 1.4;
            font-family: 'Poppins', sans-serif;
            animation: fadeInZoom 2s ease forwards;
            opacity: 0;
            padding-top: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        /* Gradient effect for first part */
        .slogan-text span:first-child {
            background: linear-gradient(90deg, #845848, #b88b5e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            white-space: nowrap;
        }

        /* Typing animation for second part */
        .slogan-text .highlight {
            color: #333;
            display: inline-block;
            border-right: 3px solid #845848; /* cursor */
            overflow: hidden;
            white-space: nowrap;
            animation: typing 4s steps(14, end) infinite, blink 0.7s step-end infinite;
        }

        /* Typing effect */
        @keyframes typing {
            0% { width: 0ch; }
            40% { width: 14ch; }   /* "Not Just Chair" = 14 characters */
            60% { width: 14ch; }
            100% { width: 0ch; }
        }

        /* Cursor blink */
        @keyframes blink {
            50% { border-color: transparent; }
        }

        /* Zoom-in effect */
        @keyframes fadeInZoom {
            0% { opacity: 0; transform: scale(0.9) translateY(30px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .slogan-text {
                font-size: 38px;
            }
        }

        @media (max-width: 992px) {
            .slogan-text {
                font-size: 32px;
                flex-direction: column;
                gap: 5px;
            }
            
            .slogan-text .highlight {
                animation: typingMobile 4s steps(14, end) infinite, blink 0.7s step-end infinite;
            }
            
            @keyframes typingMobile {
                0% { width: 0ch; }
                40% { width: 14ch; }
                60% { width: 14ch; }
                100% { width: 0ch; }
            }
        }

        @media (max-width: 768px) {
            .slogan-text {
                font-size: 28px;
            }
        }

        @media (max-width: 576px) {
            .slogan-text {
                font-size: 24px;
                flex-direction: column;
            }
            
            .slogan-text .highlight {
                font-size: 24px;
            }
        }

        @media (max-width: 400px) {
            .slogan-text {
                font-size: 20px;
            }
            
            .slogan-text .highlight {
                font-size: 20px;
            }
            
            @keyframes typingMobile {
                0% { width: 0ch; }
                40% { width: 14ch; }
                60% { width: 14ch; }
                100% { width: 0ch; max-width: 100%; }
            }
        }
    </style>

<body>
    <section class="slogan-section">
        <div class="container">
            <h2 class="slogan-text">
                <span>We Sell Comfort</span>
                <span class="highlight">Not Just Chair</span>
            </h2>
        </div>
    </section>
    </body>