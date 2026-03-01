<?php
namespace pages\wrv\food\idc_war;

require_once __DIR__ . '/aIdcWarPage.php';

class pg_index extends aIdcWarPage {
    public function getPageTitle() {
        return "I Don't Care Wars - Home";
    }
}
?>

<div style="padding: 20px; max-width: 800px; line-height: 1.6;">
    <h2>I Don't Care Wars (IDC Wars)</h2>
    
    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-top: 20px;">
        <h3 style="margin-top: 0; color: #6b4a8e;">The Concept</h3>
        <p>
            We've all been there: a group of friends or coworkers trying to decide where to go for lunch, and everyone simultaneously says, "I don't care, wherever you guys want to go." This inevitably leads to a frustrating standoff where no one wants to make the final decision.
        </p>
        <p>
            <strong>I Don't Care Wars</strong> solves this problem through democracy.
        </p>
        
        <h3 style="color: #6b4a8e; margin-top: 25px;">How It Works</h3>
        <ol style="padding-left: 20px; margin-bottom: 0;">
            <li style="margin-bottom: 10px;">
                <strong>Create a War:</strong> One person sets up a new war, gives it a deadline, and seeds the initial roster of restaurant options.
            </li>
            <li style="margin-bottom: 10px;">
                <strong>Cast Your Votes:</strong> Participants visit the voting page before the deadline and rank the restaurants from their most preferred to their least preferred using a drag-and-drop interface.
            </li>
            <li>
                <strong>Settle the War:</strong> Once the deadline passes or everyone finishes voting, the results are calculated using ranked-choice voting, determining the mathematical winner. No more arguments, no more "I don't care"—the math decides.
            </li>
        </ol>
    </div>
</div>
