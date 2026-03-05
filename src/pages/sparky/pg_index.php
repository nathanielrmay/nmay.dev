<?php
namespace pages\sparky;

require_once __DIR__ . '/aSparkyPage.php';

class pg_index extends aSparkyPage
{
    public function getPageTitle()
    {
        return "Sparky's Page";
    }
}
?>

<div style="text-align: center; padding: 40px; font-family: sans-serif;">
    <h1>Meet Sparky! 😺</h1>
    <p>Enjoy these pictures of my cat.</p>
    
    <div style="display: flex; flex-direction: column; align-items: center; gap: 30px; margin-top: 30px;">
        <img src="/media/pages/sparky/grinding.jpg" alt="Sparky grinding" style="max-width: 80%; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <img src="/media/pages/sparky/notnow.jpg" alt="Sparky saying not now" style="max-width: 80%; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <img src="/media/pages/sparky/punctual.jpg" alt="Sparky being punctual" style="max-width: 80%; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <img src="/media/pages/sparky/rawr.jpg" alt="Sparky rawr" style="max-width: 80%; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <img src="/media/pages/sparky/ready.jpg" alt="Sparky ready" style="max-width: 80%; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    </div>
</div>
