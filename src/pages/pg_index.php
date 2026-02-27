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
  }

  .neon-sign {
    position: relative;
    text-align: center;
    padding: 40px 55px;
    z-index: 1;
    background: #1a0a12;
    border-radius: 6px;
  }

  .neon-text {
    font-family: 'Sacramento', cursive;
    font-size: clamp(2rem, 4.5vw, 4rem);
    color: #ff2d95;
    text-shadow:
      0 0 7px #ff2d95,
      0 0 20px #ff2d95,
      0 0 40px #ff2d95,
      0 0 80px rgba(255,45,149,0.5),
      0 0 120px rgba(255,45,149,0.3);
    line-height: 1.4;
    animation: breathe 4s ease-in-out infinite;
  }

  .neon-attr {
    font-family: 'Sacramento', cursive;
    font-size: clamp(1rem, 2vw, 1.6rem);
    color: rgba(255,45,149,0.5);
    text-shadow:
      0 0 10px rgba(255,45,149,0.25);
    margin-top: 18px;
    text-align: right;
  }

  /* the flickering section */
  .neon-flicker {
    display: inline;
    animation: flicker 6s linear infinite;
  }

  /* wall-reflected glow underneath the sign */
  .neon-sign::after {
    display: none;
  }

  /* subtle overall brightness pulse */
  @keyframes breathe {
    0%, 100% { opacity: 1; }
    50%      { opacity: 0.92; }
  }

  /* irregular flicker for one word */
  @keyframes flicker {
    0%, 19.9%, 22%, 62.9%, 64%, 64.9%, 70%, 100% {
      opacity: 1;
      text-shadow:
        0 0 7px #ff2d95,
        0 0 20px #ff2d95,
        0 0 40px #ff2d95,
        0 0 80px rgba(255,45,149,0.5);
    }
    20%, 21.9% {
      opacity: 0.3;
      text-shadow:
        0 0 4px #ff2d95;
    }
    63%, 63.9% {
      opacity: 0.25;
      text-shadow:
        0 0 3px #ff2d95;
    }
    65%, 69.9% {
      opacity: 0.45;
      text-shadow:
        0 0 8px #ff2d95;
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
    border: 1px solid rgba(255,45,149,0.08);
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