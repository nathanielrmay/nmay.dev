<?php
namespace pages\about;

require_once __DIR__ . '/aAboutPage.php';

class pg_index extends aAboutPage {
    public function getPageTitle(): string
    {
        return "About this";
    }
}
?>

<div class="about-wrapper">
    <!-- Main Bio Column -->
    <div class="bio-column">
        <div class="bio-section">
            <h1>About this</h1>
            <p class="bio-section">
                <div class="bio-section-line-p-start">
                    Hello, my name is Nathan May. I live in Springfield Missouri and have for all 43 years of my life. I am an IT professional with 10+ years experience.
                    I have a Computer Science Degree from Missouri State University. The majority of my time in IT has been doing full stack development.
                </div>
                <br>
                <div class="bio-section-line">
                    I spent 5ish? years working on a Java Swing application, that is an odd choice for a graphics library to write a gui that was planned to be run entirely on Windows workstations.
                    I was brought in shortly after that choice was made. The guy who made that choice was the lead developer and a Linux desktop user. It was a bit inconvenient but I did admire in a way his commitment to using FOSS software.
                    I guess now-a-days you can compile c# in Linux but at that time, 2010, I am positive that was not an option.
                    At one point it was pretty gross how well I knew the Swing library, it haunts me.
                </div>
                <br>
                <div class="bio-section-line">
                    I have also done a lot of web development, for both personal projects and for work. I have written everything on this site ( Or reviewed, much of the boiler plate is done with LLM's ).
                    That includes everything supporting it as well. The site is running on a Netcup bare metal server. Set up, configured and maintained by me.
                    All data is stored in a PostgreSQL database that I configured and developed ( Sport stat mashup's, site architecture for any non static content )
                </div>
                <div class="bio-section-line">
                    About the sport stats. You will need to create an account and have it approved by me to see them for now. I don't won't to worry about the licensing
                    I would need to research if I left that part of the site freely open to the world.
                </div>
            </p>
        </div>
<!--        <div class="bio-section">-->
<!--        <h1>Why am I like this?</h1>-->
<!--            <p>-->
<!--                <div class="bio-section-line-p-start">-->
<!--                    When I was just a knee-high lad all way through high school, I was an above average local athlete.-->
<!--                    My dad is an accountant who is pretty good at explaining logic problems, statistics and odds and other fun stuff like that.-->
<!--                </div>-->
<!--                <br>-->
<!--                <div class="bio-section-line">-->
<!--                    Naturally I really enjoyed playing fantasy baseball with him. He joined a league for the 1993 MLB season and took me to the draft.-->
<!--                    I think there were 17 rounds, 11 position players, 4 starting pitchers, 2 relief pitchers.-->
<!--                    I think our first pick was Terry Pendleton, not positive about that but it would be a pretty underwhelming first pick if that is right, he was substantially worse that year than his MVP season in '92.-->
<!--                </div>-->
<!--                <br>-->
<!--                <div class="bio-section-line">-->
<!--                    In the 16th round my dad picked Rod Beck. Beck was good in '92 but only finished with 17 saves, which in 5 category rotisserie scoring games is an important stat.-->
<!--                    In '93 Beck was dominant. 48 saves, all star, 12th in MVP voting and the highest voted relief pitcher.-->
<!--                    In the 17th round my dad picked Mike Piazza. He won the Rookie of the Year award, was an all star, silver slugger and finished 9th in MVP voting ( Darren Daulton was the top catcher, two spots higher at 7th ).-->
<!--                    Those two picks pretty much won the league and got my dad a pretty good chunk of change for his effort.-->
<!--                </div>-->
<!--                <br>-->
<!--                <div class="bio-section-line">Anyway, anything you see on my site will be influenced by that.</div>-->
<!--            </p>-->
<!--        </div>-->
    </div>

    <!-- Sidebar / Action Column -->
    <div class="sidebar-column">
        <!-- Resume Card -->
        <div class="card resume-card">
            <a href="/pages/about/resume.pdf" target="_blank" class="action-button download-btn">
                <span class="icon">📄</span> Resume
            </a>
        </div>

        <!-- GitHub Card -->
        <div class="card github-card">
            <a href="https://github.com/nathanielrmay" target="_blank" class="action-button github-btn">
                Github
            </a>
        </div>

        <!-- Pikkit Card -->
        <div class="card pikkit-card">
            <h3>Pikkit</h3>
            <p>
                <div class="pikkit-card-line">
                    If you aren't familiar with Pikkit, a large part of it's functionality is as an aggregator for sportbook apps.
                    Sportsbooks are a lot like streaming apps where different vendors has different prices and different products/bets.
                    Instead of logging into each sportbook individually and recording your activity manually, Pikkit aggregates that information and provides
                    a common interface to review it.
                </div>
<!--                <div class="pikkit-card-line">-->
<!--                    I believe you can understand a lot about someone from their gambling history.-->
<!--                    You do have to create an account with them to view someone else's profile.-->
<!--                    The dollar amounts are removed from my profile for public viewers and replaced by a generic non currency 'units',-->
<!--                    I am not advertising the amount I gamble, but it is proof I can gather data and make profitable decisions using it as context.-->
<!--                </div>-->
                <div class="pikkit-card-line">I am not affiliated with them in any way. I'm just a user of the service.</div>
            </p>
            <a href="https://links.pikkit.com/user/nmay67" target="_blank" class="action-button pikkit-btn">
                Pikkit
            </a>
        </div>
    </div>
</div>

<style>
    .about-wrapper {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
        max-width: 1200px;
        margin: 20px auto;
        font-family: 'Roboto', sans-serif;
        color: #333;
    }

    @media (max-width: 768px) {
        .about-wrapper {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    .bio-column h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-top: 0;
        margin-bottom: 0;
        display: inline-block;
        padding-bottom: 5px;
    }

    /* Added margin between sections to break up the wall of text */
    .bio-section {
    }

    .bio-section p {
        margin-bottom: 50px;

    }

    .bio-section-line, .bio-section-line-p-start {
        margin-bottom: 8px;
        font-weight: bold;
    }

    .pikkit-card-line {

    }

    .bio-section-line::before, .pikkit-card-line::before {
        content: "\00a0\00a0";
    }

    .bio-section-line-p-start::before {
        content: "\00a0\00a0\00a0\00a0";
    }

    .pikkit-card-line {
        margin-bottom: 5px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .sidebar-column {
        padding-top: 20px;
    }

    .card {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .card h3 {
        margin-top: 0;
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
    }

    .card p {
    }

    .action-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 20px;
        text-decoration: none;
        font-weight: 700;
        border-radius: 4px;
        transition: transform 0.1s, box-shadow 0.1s;
        text-align: center;
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .download-btn {
        background-color: #2b2b2b;
        color: #fff;
    }

    .download-btn:hover {
        background-color: #000;
        color: #fff;
    }

    .github-btn {
        background-color: #24292e;
        color: #fff;
    }

    .github-btn:hover {
        background-color: #444;
        color: #fff;
    }

    .pikkit-btn {
        background-color: #4A148C;
        color: #fff;
    }

    .pikkit-btn:hover {
        background-color: #6A1B9A;
        color: #fff;
    }
</style>
