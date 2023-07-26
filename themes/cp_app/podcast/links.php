<?= helper('page') ?>

<!DOCTYPE html>
<html lang="<?= service('request')
    ->getLocale() ?>">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" type="image/x-icon" href="<?= get_site_icon_url('ico') ?>" />
    <link rel="apple-touch-icon" href="<?= get_site_icon_url('180') ?>">
    <link rel="manifest" href="<?= route_to('podcast-webmanifest', esc($podcast->handle)) ?>">
    <meta name="theme-color" content="<?= \App\Controllers\WebmanifestController::THEME_COLORS[service('settings')->get('App.theme')]['theme'] ?>">
    <script>
    // Check that service workers are supported
    if ('serviceWorker' in navigator) {
        // Use the window load event to keep the page load performant
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js');
        });
    }
    </script>

    <?= $metatags ?>

    <link rel='stylesheet' type='text/css' href='<?= route_to('themes-colors-css') ?>' />
    <?= service('vite')
        ->asset('styles/index.css', 'css') ?>
    <?= service('vite')
        ->asset('js/app.ts', 'js') ?>
    <?= service('vite')
        ->asset('js/podcast.ts', 'js') ?>
</head>

<body class="flex flex-col min-h-screen mx-auto bg-base theme-<?= service('settings')
        ->get('App.theme') ?>">
    <?php if (can_user_interact()): ?>
        <div class="col-span-full">
            <?= $this->include('_admin_navbar') ?>
        </div>
    <?php endif; ?>

    <header class="relative items-center justify-center w-full bg-center bg-no-repeat bg-cover" style="background-image: url('<?= get_podcast_banner_url($podcast, 'small') ?>');">
        <div class="absolute bottom-0 left-0 w-full h-full backdrop-gradient-accent bg-blend-darken"></div>
        <div class="z-10 flex flex-col items-center justify-center w-full h-full gap-2 py-12 backdrop-blur-xl ">
            <img src="<?= $podcast->cover->thumbnail_url ?>" alt="<?= esc($podcast->title) ?>" class="rounded-full shadow-2xl h-28 ring-2 ring-background-elevated aspect-square" loading="lazy" />
            <h1 class="flex flex-col items-center mt-2 text-2xl font-bold leading-none line-clamp-2 md:leading-none font-display"><?= esc($podcast->title) ?><span class="ml-1 font-sans text-base font-normal">@<?= esc($podcast->handle) ?></span></h1>
            <div class="z-10 flex flex-wrap items-center justify-center gap-2 p-2 mt-6 shadow-xl rounded-conditional-full bg-accent-base shadow-accent/20">
                <?php if (in_array(true, array_column($podcast->fundingPlatforms, 'is_visible'), true)): ?>
                    <button class="inline-flex items-center px-4 text-xs font-semibold leading-8 tracking-wider text-red-600 uppercase bg-white rounded-full shadow hover:text-red-500 focus:ring-accent" data-toggle="funding-links" data-toggle-class="hidden"><Icon glyph="heart" class="mr-2 text-sm"></Icon><?= lang('Podcast.sponsor') ?></button>
                <?php endif; ?>
                <?= anchor_popup(
                    route_to('follow', esc($podcast->handle)),
                    icon(
                        'social/castopod',
                        'mr-2 text-xl text-black/75 group-hover:text-black',
                    ) . lang('Podcast.follow'),
                    [
                        'width'  => 420,
                        'height' => 620,
                        'class'  => 'group inline-flex items-center px-4 text-xs tracking-wider font-semibold text-black uppercase rounded-full leading-8 shadow focus:ring-accent bg-white',
                    ],
                ) ?>
                <a href="<?= $podcast->feed_url ?>" title="<?= lang('Podcast.feed') ?>" data-tooltip="bottom" class="flex items-center justify-center w-8 h-8 p-1 text-xl text-orange-500 rounded-full shadow bg-elevated focus:ring-accent" target="_blank" rel="noopener noreferrer"><?= icon('rss') ?></a>
            </div>
        </div>
    </header>
    <main class="grid w-full max-w-2xl gap-4 px-4 py-6 mx-auto sm:grid-cols-2">
        <?php foreach ($podcast->podcastingPlatforms as $podcastingPlatform): ?>
            <?php if ($podcastingPlatform->is_visible): ?>
                <a 
                class="inline-flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-semibold leading-5 bg-white border-2 rounded-full shadow-xs gap-x-2 focus:ring-accent border-accent-base text-accent-base hover:border-accent-hover hover:text-accent-hover"
                href="<?= $podcastingPlatform->link_url ?>"
                target="_blank"
                rel="noopener noreferrer"><?= icon($podcastingPlatform->slug, 'text-xl mr-auto', $podcastingPlatform->type) ?><span class="mr-auto -ml-8"><?= $podcastingPlatform->label ?></span>
            </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </main>
    <footer class="flex items-center justify-center w-full max-w-2xl gap-4 p-4 mx-auto">
    <?php foreach ($podcast->socialPlatforms as $socialPlatform): ?>
        <?php if ($socialPlatform->is_visible): ?>
                <?= anchor(
                    esc($socialPlatform->link_url),
                    icon(
                        esc($socialPlatform->slug),
                        '',
                        $socialPlatform->type
                    ),
                    [
                        'class'        => 'focus:ring-accent rounded-full text-4xl text-skin-muted hover:text-skin-base w-8 h-8 items-center inline-flex justify-center',
                        'target'       => '_blank',
                        'rel'          => 'noopener noreferrer',
                        'data-tooltip' => 'bottom',
                        'title'        => $socialPlatform->label,
                    ],
                ) ?>
            <?php endif; ?>
    <?php endforeach; ?>
    </footer>

    <?php if (in_array(true, array_column($podcast->fundingPlatforms, 'is_visible'), true)): ?>
        <?= $this->include('podcast/_partials/funding_links_modal') ?>
    <?php endif; ?>

</body>
