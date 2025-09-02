<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Balaji Preloader</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #preloader {
            position: fixed;
            inset: 0;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            overflow: hidden;
        }

        .preloader-inner {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #balajiMorph {
            width: 160px;
            height: 160px;
            filter: drop-shadow(0 0 15px rgba(255, 0, 0, 0.4));
        }

        @keyframes pulse {
            0% {
                filter: drop-shadow(0 0 5px rgba(255, 0, 0, 0.3));
            }

            50% {
                filter: drop-shadow(0 0 25px rgba(255, 0, 0, 0.7));
            }

            100% {
                filter: drop-shadow(0 0 5px rgba(255, 0, 0, 0.3));
            }
        }

        .pulsing {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body>
    <!-- Preloader -->
    <div id="preloader" aria-hidden="true">
        <div class="preloader-inner">
            <svg id="balajiMorph" viewBox="0 0 200 200" role="img">
                <path id="morphPath" fill="#d62828"
                    d="M90,50 L110,50 L110,98 Q118,86 136,86 Q158,86 158,106 Q158,132 136,132 Q118,132 110,118 L110,150 L90,150 Z" />
            </svg>
        </div>
    </div>

    <!-- Anime.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
    <script>
        (function () {
            const pathEl = document.getElementById("morphPath");
            const inner = document.querySelector("#preloader .preloader-inner");
            const overlay = document.getElementById("preloader");

            // Shapes
            const B =
                "M90,50 L110,50 L110,98 Q118,86 136,86 Q158,86 158,106 Q158,132 136,132 Q118,132 110,118 L110,150 L90,150 Z";
            const CHAIR =
                "M60,150 L60,112 L78,112 L78,62 Q78,48 92,48 L138,48 Q150,48 150,60 L150,112 L158,112 L158,130 L112,130 L112,150 L96,150 L96,130 L60,130 Z";
            const FINAL_B =
                "M140 20 C120 10 100 40 100 70 C100 100 140 100 150 130 C160 160 140 180 120 180 C90 180 80 140 100 110 C60 120 40 160 60 180 L75 185 L85 160 C100 140 140 140 150 100 C160 70 150 30 140 20 Z";

            // Morphing timeline: B → Chair → Final B
            const loopTL = anime.timeline({ loop: true, easing: "easeInOutCubic" });
            loopTL
                .add({ targets: pathEl, d: [{ value: CHAIR }], duration: 1000 })
                .add({ targets: pathEl, d: [{ value: FINAL_B }], duration: 1000 })
                .add({ targets: pathEl, d: [{ value: B }], duration: 1000 });

            // Exit → fly to header
            function flyToHeader() {
                loopTL.pause();
                pathEl.setAttribute("d", FINAL_B);

                const target = document.getElementById("siteLogo");
                const targetRect = target
                    ? target.getBoundingClientRect()
                    : { left: 20, top: 20, width: 60, height: 60 };

                const svgRect = inner.getBoundingClientRect();
                const translateX = (targetRect.left + targetRect.width / 2) - (svgRect.left + svgRect.width / 2);
                const translateY = (targetRect.top + targetRect.height / 2) - (svgRect.top + svgRect.height / 2);
                const scale = Math.min(targetRect.width / svgRect.width, targetRect.height / svgRect.height);

                anime({
                    targets: inner,
                    translateX: translateX,
                    translateY: translateY,
                    scale: scale,
                    duration: 1200,
                    easing: "easeInOutExpo",
                    complete: () => {
                        overlay.style.transition = "opacity .6s ease";
                        overlay.style.opacity = 0;
                        setTimeout(() => overlay.remove(), 600);
                    }
                });
            }

            // On page load
            window.addEventListener("load", flyToHeader);

            // Fallback timeout
            setTimeout(() => {
                if (document.body.contains(overlay)) flyToHeader();
            }, 6000);

            // Pulse glow
            document.getElementById("balajiMorph").classList.add("pulsing");
        })();
    </script>
</body>

</html>