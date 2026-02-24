<?php
  namespace pages;
  use lib\contracts\aPage;

class pg_404 extends aPage {

      public function getPageTitle() { return "404 Not Found"; }
  }
?>

<canvas id="canvas-404"></canvas>
<script>
    function start404Canvas({
                                canvasId = 'canvas-404',
                                background = '#fff4f2',
                                textColor = '#140b25',
                                glowColor = '#ffffff',
                                neonLeft = 'rgba(255, 25, 180, 0.65)',
                                neonRight = 'rgba(0, 247, 255, 0.65)',
                                glitchSlices = 6,
                            } = {}) {
        const canvas = document.getElementById(canvasId) || (() => {
            const c = document.createElement('canvas');
            c.id = canvasId;
            document.body.prepend(c);
            return c;
        })();
        const ctx = canvas.getContext('2d');
        const container = canvas.parentElement || document.body;

        const resize = () => {
            const { width, height } = container.getBoundingClientRect();
            canvas.width = width || window.innerWidth;
            canvas.height = height || window.innerHeight;
        };
        resize();
        window.addEventListener('resize', resize);

        function drawFrame(time) {
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.fillStyle = background;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;

            const mainFontSize = Math.min(canvas.width, canvas.height) * 0.26;
            const subFontSize = mainFontSize * 0.22;

            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Neon offsets for the “404”
            const flicker = Math.sin(time * 0.004) * 2;
            const offsets = [
                { dx: -3, dy: -1, color: neonLeft },
                { dx: 3, dy: 1, color: neonRight },
                { dx: 0, dy: 0, color: textColor },
                { dx: flicker * 0.4, dy: 0, color: glowColor, glow: true },
            ];

            ctx.font = `800 ${mainFontSize}px 'Space Grotesk', 'Futura', sans-serif`;
            offsets.forEach((o, idx) => {
                ctx.fillStyle = o.color;
                if (o.glow) {
                    ctx.shadowColor = glowColor;
                    ctx.shadowBlur = 30 + Math.sin(time * 0.01) * 6;
                } else {
                    ctx.shadowBlur = 0;
                }
                ctx.fillText('404', centerX + o.dx, centerY - subFontSize * 0.4 + o.dy);
            });
            ctx.shadowBlur = 0;

            // Subtitle
            ctx.font = `600 ${subFontSize}px 'Space Grotesk', 'Futura', sans-serif`;
            ctx.fillStyle = textColor;
            ctx.globalAlpha = 0.9;
            ctx.fillText('— NOT FOUND —', centerX, centerY + mainFontSize * 0.35);

            // Added saying
            const sayingFontSize = subFontSize * 0.6;
            ctx.font = `italic 400 ${sayingFontSize}px 'Space Grotesk', 'Futura', sans-serif`;
            ctx.fillText('how is it you have come to arrive here?', centerX, centerY + mainFontSize * 0.35 + subFontSize * 1.2);

            ctx.globalAlpha = 1;

            // Glitch / flicker lines across the text
            for (let i = 0; i < glitchSlices; i++) {
                const sliceOffset = (Math.random() - 0.5) * mainFontSize * 0.3;
                const sliceHeight = mainFontSize * (0.015 + Math.random() * 0.02);
                const opacity = 0.12 + Math.random() * 0.15;
                ctx.fillStyle = i % 2
                    ? `rgba(255, 25, 180, ${opacity})`
                    : `rgba(0, 247, 255, ${opacity})`;
                ctx.fillRect(
                    centerX - canvas.width,
                    centerY + sliceOffset,
                    canvas.width * 2,
                    sliceHeight
                );
            }

            // Thin flicker scan line
            const scanWidth = canvas.width * 0.8;
            const scanHeight = Math.max(1, mainFontSize * 0.01);
            ctx.fillStyle = `rgba(0, 0, 0, ${0.05 + Math.random() * 0.07})`;
            ctx.fillRect(
                centerX - scanWidth / 2,
                centerY - mainFontSize * 0.4 + (Math.random() - 0.5) * mainFontSize * 0.1,
                scanWidth,
                scanHeight
            );

            requestAnimationFrame(drawFrame);
        }

        requestAnimationFrame(drawFrame);
    }

    document.addEventListener('DOMContentLoaded', () =>
        start404Canvas({
            background: '#fff4f2', // matches your site
        })
    );
</script>
