<?php
  namespace pages;
  use lib\contracts\aPage;

class pg_index extends aPage {

      public function getPageTitle(): string
      { return "Home"; }
  }
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Sacramento&display=swap');

  .neon-wall {
    position: relative;
    width: 100%;
    height: 82vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    /* Define CSS variables that the animation will manipulate */
    --neon-clr: #ff2d95;
    --neon-glow-1: rgba(255,45,149,0.5);
    --neon-glow-2: rgba(255,45,149,0.3);
    animation: color-cycle 24s linear infinite;
  }

  /* Define the color cycling animation on the container so children inherit the variables */
  @keyframes color-cycle {
    /* Pink */
    0%, 25% {
      --neon-clr: #ff2d95;
      --neon-glow-1: rgba(255,45,149,0.5);
      --neon-glow-2: rgba(255,45,149,0.3);
    }
    /* Light Blue */
    33%, 58% {
      --neon-clr: #00f0ff;
      --neon-glow-1: rgba(0,240,255,0.5);
      --neon-glow-2: rgba(0,240,255,0.3);
    }
    /* Light Green */
    66%, 91% {
      --neon-clr: #39ff14;
      --neon-glow-1: rgba(57,255,20,0.5);
      --neon-glow-2: rgba(57,255,20,0.3);
    }
    /* Back to Pink */
    100% {
      --neon-clr: #ff2d95;
      --neon-glow-1: rgba(255,45,149,0.5);
      --neon-glow-2: rgba(255,45,149,0.3);
    }
  }

  .neon-sign {
    position: relative;
    text-align: center;
    padding: 40px 55px;
    z-index: 1;
    background: #1a0a12;
    border-radius: 6px;
  }

  /* Note: standard CSS animation on variables requires @property in modern browsers for smooth fading. 
     Since @property support isn't 100% universal yet, if it snaps instead of fading gracefully, 
     the fallback is still a cool color change. */
  @property --neon-clr { syntax: '<color>'; inherits: true; initial-value: #ff2d95; }
  @property --neon-glow-1 { syntax: '<color>'; inherits: true; initial-value: rgba(255,45,149,0.5); }
  @property --neon-glow-2 { syntax: '<color>'; inherits: true; initial-value: rgba(255,45,149,0.3); }

  .neon-text {
    font-family: 'Sacramento', cursive;
    font-size: clamp(2rem, 4.5vw, 4rem);
    color: var(--neon-clr);
    text-shadow:
      0 0 7px var(--neon-clr),
      0 0 20px var(--neon-clr),
      0 0 40px var(--neon-clr),
      0 0 80px var(--neon-glow-1),
      0 0 120px var(--neon-glow-2);
    line-height: 1.4;
    position: relative;
  }

  /* Added a pseudo-element for the breathing effect so it doesn't fight with the flicker animation on text-shadow */
  .neon-text::before {
    content: '';
    position: absolute;
    inset: -20px;
    background: var(--neon-clr);
    filter: blur(40px);
    opacity: 0.15;
    z-index: -1;
    animation: breathe 4s ease-in-out infinite;
    pointer-events: none;
  }

  .neon-attr {
    font-family: 'Sacramento', cursive;
    font-size: clamp(1rem, 2vw, 1.6rem);
    color: var(--neon-glow-1);
    text-shadow:
      0 0 10px rgba(255,255,255,0.2);
    margin-top: 18px;
    text-align: right;
  }

  /* subtle overall brightness pulse */
  @keyframes breathe {
    0%, 100% { opacity: 0.15; }
    50%      { opacity: 0.05; }
  }

  /* the flickering section */
  .neon-flicker {
    display: inline;
    animation: flicker 6s linear infinite;
  }

  /* irregular flicker for one word */
  @keyframes flicker {
    0%, 19.9%, 22%, 62.9%, 64%, 64.9%, 70%, 100% {
      opacity: 1;
      text-shadow:
        0 0 7px var(--neon-clr),
        0 0 20px var(--neon-clr),
        0 0 40px var(--neon-clr),
        0 0 80px var(--neon-glow-1);
    }
    20%, 21.9% {
      opacity: 0.3;
      text-shadow:
        0 0 4px var(--neon-clr);
    }
    63%, 63.9% {
      opacity: 0.25;
      text-shadow:
        0 0 3px var(--neon-clr);
    }
    65%, 69.9% {
      opacity: 0.45;
      text-shadow:
        0 0 8px var(--neon-clr);
    }
  }

  /* mounting screws */
  .screw {
    position: absolute;
    width: 6px; height: 6px;
    background: radial-gradient(circle, #665 40%, #332 100%);
    border-radius: 50%;
    box-shadow: 0 0 3px rgba(0,0,0,0.6);
    z-index: 2;
  }
  .screw--tl { top: 12px;  left: 20px; }
  .screw--tr { top: 12px;  right: 20px; }
  .screw--bl { bottom: 12px; left: 20px; }
  .screw--br { bottom: 12px; right: 20px; }

  /* thin tube connector lines from screws to text */
  .neon-sign::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    border: 1px solid rgba(255,255,255,0.03);
    border-radius: 4px;
    pointer-events: none;
  }
</style>

<div class="neon-wall">
  <div class="neon-sign">
    <div class="screw screw--tl"></div>
    <div class="screw screw--tr"></div>
    <div class="screw screw--bl"></div>
    <div class="screw screw--br"></div>
    <div class="neon-text">
      Time is a strange thing.<br>
      When we don't need it, it is nothing.<br>
      Then, <span class="neon-flicker">suddenly</span>,<br>
      there is nothing else.
    </div>
    <div class="neon-attr">— Carlo Rovelli</div>
  </div>
</div>