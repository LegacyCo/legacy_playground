<?php
/**
 * Template Name: Fenben® Landing Page
 * Template Post Type: page
 *
 * @package HappyHealingStore
 * @version 1.0
 *
 * INSTALLATION:
 * 1. Upload this file to /wp-content/themes/YOUR-THEME/
 * 2. Go to Pages > Edit your Fenben page > Page Attributes > Template > "Fenben® Landing Page"
 * 3. WooCommerce required for live product data (falls back to static data otherwise)
 *
 * PRODUCT IMAGE UPLOAD:
 * Upload product images to WordPress Media Library, naming them:
 *   fenben-hero-couple-golden-field.jpg
 *   fenben-story-woman-hiking.jpg
 *   fenben-pure-powder.jpg
 *   fenben-bio-capsules.jpg
 *   fenben-222mg-tablets.jpg
 *   fenben-500mg-tablets.jpg
 *   fenben-750mg-tablets.jpg
 *   fenben-trio.jpg
 *   fenben-pure-capsules.jpg
 * Then update the $fenben_products array $img_url values below.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Enqueue Google Fonts on this template
function fenben_landing_enqueue( $hook ) {
    if ( is_page_template( 'page-fenben-landing.php' ) ) {
        wp_enqueue_style(
            'fenben-google-fonts',
            'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap',
            [], null
        );
    }
}
add_action( 'wp_enqueue_scripts', 'fenben_landing_enqueue' );

// ----------------------------------------------------------------
// Helper: render star rating
// ----------------------------------------------------------------
function fenben_stars( $n = 5 ) {
    $out = '';
    for ( $i = 0; $i < 5; $i++ ) { $out .= $i < $n ? '&#9733;' : '&#9734;'; }
    return $out;
}

// ----------------------------------------------------------------
// Helper: try to pull live WooCommerce product by slug
// ----------------------------------------------------------------
function fenben_get_wc_data( $slug ) {
    if ( ! function_exists( 'wc_get_product' ) ) return null;
    $page = get_page_by_path( $slug, OBJECT, 'product' );
    if ( ! $page ) return null;
    $p = wc_get_product( $page->ID );
    if ( ! $p ) return null;
    return [
        'name'       => $p->get_name(),
        'url'        => get_permalink( $page->ID ),
        'desc'       => wp_strip_all_tags( $p->get_short_description() ?: $p->get_description() ),
        'price'      => $p->get_price_html(),
        'img'        => get_the_post_thumbnail( $page->ID, 'medium', ['class'=>'product-img','loading'=>'lazy'] ),
        'rating'     => (float) $p->get_average_rating(),
    ];
}

// ----------------------------------------------------------------
// Product definitions
// ----------------------------------------------------------------
$fenben_products = [
    [
        'name'   => 'Fenben® Pure Powder',
        'slug'   => 'fenben-pure-powder',
        'url'    => 'https://thehappyhealingstore.com/product/fenben-pure-powder/',
        'desc'   => 'Pure fenbendazole powder for flexible dosing. Lab-verified pharmaceutical grade.',
        'badge'  => 'Best Seller',
        'bcolor' => '#C8973A',
        'img'    => 'fenben-pure-powder.jpg',
        'alt'    => 'Fenben® Pure Powder 50g amber bottle with golden flowers',
    ],
    [
        'name'   => 'Fenben® Bio Capsules',
        'slug'   => 'fenben-bio-capsules',
        'url'    => 'https://thehappyhealingstore.com/product/fenben-bio-capsules/',
        'desc'   => 'Enhanced bioavailability formula in capsule form. 90 capsules · 222mg per bottle.',
        'badge'  => 'Enhanced Absorption',
        'bcolor' => '#C8973A',
        'img'    => 'fenben-bio-capsules.jpg',
        'alt'    => 'Fenben® Bio Capsules 90 capsules 222mg amber bottle with golden flowers',
    ],
    [
        'name'   => 'Fenben® 222mg Tablets',
        'slug'   => 'fenben-222mg-tablets',
        'url'    => 'https://thehappyhealingstore.com/product/fenben-222mg-tablets/',
        'desc'   => 'Precise 222mg pharmaceutical-grade tablets. 90 tablets per bottle.',
        'badge'  => 'Most Popular',
        'bcolor' => '#C8973A',
        'img'    => 'fenben-222mg-tablets.jpg',
        'alt'    => 'Fenben® 222mg Tablets 90 tablets amber bottle with golden flowers',
    ],
    [
        'name'   => 'Fenben® 500mg Tablets',
        'slug'   => 'fenben-500mg-tablets',
        'url'    => 'https://thehappyhealingstore.com/product/fenben-500mg-tablets/',
        'desc'   => 'Advanced 500mg formula for higher-strength protocols. 90 tablets per bottle.',
        'badge'  => 'Higher Strength',
        'bcolor' => '#C8973A',
        'img'    => 'fenben-500mg-tablets.jpg',
        'alt'    => 'Fenben® 500mg Tablets 90 tablets amber bottle with golden flowers',
    ],
    [
        'name'   => 'Fenben® 750mg Tablets',
        'slug'   => 'happy-healing-fenben-tablets-750',
        'url'    => 'https://thehappyhealingstore.com/product/happy-healing-fenben-tablets-750/',
        'desc'   => 'Maximum strength 750mg tablets for advanced wellness protocols. 90 tablets.',
        'badge'  => 'Max Strength',
        'bcolor' => '#C8973A',
        'img'    => 'fenben-750mg-tablets.jpg',
        'alt'    => 'Fenben® 750mg Tablets 90 tablets amber bottle with golden flowers',
    ],
    [
        'name'   => 'Fenben® Trio',
        'slug'   => 'trio',
        'url'    => 'https://thehappyhealingstore.com/product/trio/',
        'desc'   => 'Fenben® Trio blend â 90 tablets · 694mg. Three-compound formula for comprehensive support.',
        'badge'  => 'Best Value',
        'bcolor' => '#2D5016',
        'img'    => 'fenben-trio.jpg',
        'alt'    => 'Fenben® Trio 90 tablets 694mg amber bottle with golden flowers',
    ],
    [
        'name'   => 'Fenben® Pure Capsules',
        'slug'   => 'fenben-pure-capsules',
        'url'    => 'https://thehappyhealingstore.com/product/fenben-pure-capsules/',
        'desc'   => 'Pure fenbendazole formula in capsule form. 90 capsules · 250mg per capsule.',
        'badge'  => 'New Formula',
        'bcolor' => '#5A2D82',
        'img'    => 'fenben-pure-capsules.jpg',
        'alt'    => 'Fenben® Pure Capsules amber bottle with golden flowers',
    ],
];

// ----------------------------------------------------------------
// Render product card (WooCommerce-aware)
// ----------------------------------------------------------------
function fenben_render_card( $p, $idx ) {
    $wc    = fenben_get_wc_data( $p['slug'] );
    $name  = esc_html( $wc ? $wc['name']  : $p['name'] );
    $url   = esc_url(  $wc ? $wc['url']   : $p['url'] );
    $desc  = esc_html( $wc ? $wc['desc']  : $p['desc'] );
    $price = $wc ? $wc['price'] : 'View Pricing';
    $stars = fenben_stars( $wc ? $wc['rating'] : 5 );
    $badge = esc_html( $p['badge'] );
    $bc    = esc_attr( $p['bcolor'] );
    $alt   = esc_attr( $p['alt'] );

    // Determine image: WooCommerce thumbnail > uploaded file > placeholder
    if ( $wc && ! empty( $wc['img'] ) ) {
        $img_block = $wc['img'];
    } else {
        $img_url = content_url( '/uploads/fenben/' . $p['img'] );
        $img_block = '<img src="' . esc_url( $img_url ) . '" alt="' . $alt . '"
                          class="product-img" loading="lazy"
                          onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">';
        $img_block .= '<div class="product-img-placeholder" style="display:none">
                          <span class="pip-label">Fenben&#174;</span>
                          <span class="pip-name">' . $name . '</span>
                        </div>';
    }
    ?>
    <article class="product-card" data-index="<?php echo $idx; ?>">
        <div class="product-img-wrap">
            <?php echo $img_block; ?>
            <span class="product-badge" style="background:<?php echo $bc; ?>"><?php echo $badge; ?></span>
        </div>
        <div class="product-body">
            <h3 class="product-name"><?php echo $name; ?></h3>
            <p class="product-desc"><?php echo $desc; ?></p>
            <div class="product-meta">
                <span class="stars"><?php echo $stars; ?></span>
                <span class="product-price"><?php echo $price; ?></span>
            </div>
            <a href="<?php echo $url; ?>" class="product-cta">View Product</a>
        </div>
    </article>
    <?php
}

// ================================================================
// PAGE OUTPUT
// ================================================================
get_header();
?>
<style>

    /* ============================================================
       FENBEN® LANDING PAGE — THE HAPPY HEALING STORE
       Version 1.0 | 2025
    ============================================================ */

    :root {
      --color-primary: #2D5016;
      --color-secondary: #3D6B1E;
      --color-accent: #C8973A;
      --color-accent-light: #E8C57A;
      --color-accent-dark: #A07828;
      --color-bg: #FAF8F4;
      --color-bg-alt: #F0EDE4;
      --color-bg-dark: #1A2E0A;
      --color-text: #1C1C1A;
      --color-text-muted: #6B6B5E;
      --color-text-light: #A8A89A;
      --color-white: #FFFFFF;
      --color-border: #E4DDD0;
      --color-success: #4A7C2F;
      --font-heading: "Playfair Display", Georgia, serif;
      --font-body: "Inter", system-ui, sans-serif;
      --radius-sm: 8px;
      --radius-md: 16px;
      --radius-lg: 24px;
      --shadow-sm: 0 2px 8px rgba(45,80,22,0.08);
      --shadow-md: 0 8px 32px rgba(45,80,22,0.12);
      --shadow-lg: 0 16px 64px rgba(45,80,22,0.16);
      --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
      --max-width: 1280px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; font-size: 16px; }

    body {
      font-family: var(--font-body);
      background: var(--color-bg);
      color: var(--color-text);
      line-height: 1.6;
      overflow-x: hidden;
    }

    h1,h2,h3,h4,h5 {
      font-family: var(--font-heading);
      line-height: 1.2;
      color: var(--color-primary);
    }

    a { color: inherit; text-decoration: none; }
    img { max-width: 100%; height: auto; display: block; }
    button { cursor: pointer; font-family: var(--font-body); }

    .container {
      max-width: var(--max-width);
      margin: 0 auto;
      padding: 0 24px;
    }

    .section-label {
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--color-accent);
      margin-bottom: 12px;
    }

    .section-title {
      font-size: clamp(1.75rem, 3vw, 2.75rem);
      color: var(--color-primary);
      margin-bottom: 16px;
    }

    .section-subtitle {
      font-size: 1.0625rem;
      color: var(--color-text-muted);
      max-width: 560px;
      margin: 0 auto 48px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 32px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.9375rem;
      border: 2px solid transparent;
      transition: var(--transition);
      cursor: pointer;
      white-space: nowrap;
    }

    .btn-primary {
      background: var(--color-primary);
      color: var(--color-white);
      border-color: var(--color-primary);
    }
    .btn-primary:hover {
      background: var(--color-secondary);
      border-color: var(--color-secondary);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .btn-accent {
      background: var(--color-accent);
      color: var(--color-white);
      border-color: var(--color-accent);
    }
    .btn-accent:hover {
      background: var(--color-accent-dark);
      border-color: var(--color-accent-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .btn-outline {
      background: transparent;
      color: var(--color-primary);
      border-color: var(--color-primary);
    }
    .btn-outline:hover {
      background: var(--color-primary);
      color: var(--color-white);
      transform: translateY(-2px);
    }

    .btn-outline-white {
      background: transparent;
      color: var(--color-white);
      border-color: var(--color-white);
    }
    .btn-outline-white:hover {
      background: var(--color-white);
      color: var(--color-primary);
    }

    /* Stars */
    .stars { color: var(--color-accent); font-size: 0.875rem; letter-spacing: 2px; }

    /* Fade-in animation */
    .fade-in-up {
      opacity: 0;
      transform: translateY(32px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .fade-in-up.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* ============================================================
       1. ANNOUNCEMENT BAR
    ============================================================ */
    #announcement-bar {
      background: var(--color-primary);
      color: var(--color-white);
      text-align: center;
      padding: 10px 48px;
      font-size: 0.8125rem;
      font-weight: 500;
      position: relative;
      z-index: 100;
      letter-spacing: 0.01em;
    }
    #announcement-bar a {
      color: var(--color-accent-light);
      font-weight: 600;
      margin-left: 8px;
    }
    #announcement-bar a:hover { text-decoration: underline; }
    .announce-close {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: rgba(255,255,255,0.7);
      font-size: 1.25rem;
      line-height: 1;
      padding: 4px 8px;
      cursor: pointer;
    }
    .announce-close:hover { color: white; }

    /* ============================================================
       2. NAVIGATION
    ============================================================ */
    #site-nav {
      position: sticky;
      top: 0;
      z-index: 999;
      background: rgba(250,248,244,0.95);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--color-border);
      transition: box-shadow 0.3s ease;
    }
    #site-nav.scrolled {
      box-shadow: 0 4px 24px rgba(45,80,22,0.12);
    }
    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 72px;
      max-width: var(--max-width);
      margin: 0 auto;
      padding: 0 24px;
    }
    .nav-logo {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }
    .nav-logo-main {
      font-family: var(--font-heading);
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--color-primary);
      letter-spacing: -0.01em;
    }
    .nav-logo-sub {
      font-size: 0.625rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: var(--color-text-muted);
    }
    .nav-links {
      display: flex;
      list-style: none;
      gap: 32px;
      align-items: center;
    }
    .nav-links a {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--color-text-muted);
      transition: color 0.2s;
    }
    .nav-links a:hover { color: var(--color-primary); }
    .nav-cta { display: flex; align-items: center; gap: 12px; }
    .nav-shop-btn {
      background: var(--color-accent);
      color: white;
      padding: 10px 24px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.875rem;
      border: none;
      cursor: pointer;
      transition: var(--transition);
    }
    .nav-shop-btn:hover {
      background: var(--color-accent-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 16px rgba(200,151,58,0.3);
    }

    /* Mobile nav */
    .hamburger {
      display: none;
      flex-direction: column;
      gap: 5px;
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
    }
    .hamburger span {
      display: block;
      width: 24px;
      height: 2px;
      background: var(--color-primary);
      border-radius: 2px;
      transition: var(--transition);
    }
    .hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(5px,5px); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(5px,-5px); }

    #mobile-menu {
      display: none;
      position: fixed;
      inset: 0;
      background: var(--color-bg);
      z-index: 998;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 32px;
    }
    #mobile-menu.open { display: flex; }
    #mobile-menu a {
      font-family: var(--font-heading);
      font-size: 1.75rem;
      color: var(--color-primary);
      font-weight: 600;
    }
    #mobile-menu a:hover { color: var(--color-accent); }

    @media (max-width: 1023px) {
      .nav-links { display: none; }
      .hamburger { display: flex; }
    }
    @media (max-width: 767px) {
      .nav-shop-btn { display: none; }
    }

    /* ============================================================
       3. HERO
    ============================================================ */
    #hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      position: relative;
      overflow: hidden;
      background: var(--color-bg);
    }

    /* Botanical background pattern */
    #hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 200 200'%3E%3Cpath d='M20 100 Q35 60 50 100 Q65 140 80 100' fill='none' stroke='%232D5016' stroke-opacity='0.04' stroke-width='2'/%3E%3Cpath d='M120 40 Q135 10 150 40 Q165 70 180 40' fill='none' stroke='%232D5016' stroke-opacity='0.04' stroke-width='1.5'/%3E%3Ccircle cx='100' cy='150' r='3' fill='%23C8973A' fill-opacity='0.06'/%3E%3C/svg%3E");
      background-size: 200px 200px;
      pointer-events: none;
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 48px;
      align-items: center;
      min-height: 100vh;
      padding: 100px 24px 64px;
      max-width: var(--max-width);
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    .hero-content { max-width: 600px; }

    .hero-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(200,151,58,0.12);
      border: 1px solid rgba(200,151,58,0.3);
      color: var(--color-accent-dark);
      padding: 6px 16px;
      border-radius: 50px;
      font-size: 0.8125rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      margin-bottom: 24px;
    }

    .hero-title {
      font-size: clamp(2.25rem, 4.5vw, 3.75rem);
      font-weight: 700;
      color: var(--color-primary);
      line-height: 1.1;
      margin-bottom: 24px;
    }
    .hero-title em {
      font-style: italic;
      color: var(--color-accent);
    }

    .hero-sub {
      font-size: 1.125rem;
      color: var(--color-text-muted);
      line-height: 1.7;
      margin-bottom: 40px;
      max-width: 520px;
    }

    .hero-ctas {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }

    .hero-badges {
      display: flex;
      gap: 24px;
      flex-wrap: wrap;
    }
    .hero-badge {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.8125rem;
      font-weight: 600;
      color: var(--color-success);
    }
    .hero-badge::before {
      content: "✓";
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 18px;
      height: 18px;
      background: var(--color-success);
      color: white;
      border-radius: 50%;
      font-size: 0.625rem;
      font-weight: 700;
      flex-shrink: 0;
    }

    .hero-image-wrap {
      position: relative;
      height: 600px;
      border-radius: var(--radius-lg);
      overflow: hidden;
    }

    .hero-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
    }

    /* Placeholder for hero image until real image is set */
    .hero-img-placeholder {
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #2D5016 0%, #4A7C2F 40%, #C8973A 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(255,255,255,0.6);
      font-size: 0.875rem;
      font-style: italic;
      text-align: center;
      padding: 32px;
    }

    .hero-image-card {
      position: absolute;
      bottom: 32px;
      left: -24px;
      background: white;
      border-radius: var(--radius-md);
      padding: 16px 20px;
      box-shadow: var(--shadow-lg);
      display: flex;
      align-items: center;
      gap: 12px;
      max-width: 220px;
    }
    .hero-card-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .hero-card-text { font-size: 0.75rem; }
    .hero-card-text strong { display: block; color: var(--color-primary); font-size: 0.875rem; }

    .scroll-indicator {
      position: absolute;
      bottom: 32px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      color: var(--color-text-light);
      font-size: 0.6875rem;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      animation: bounce 2s infinite;
    }
    @keyframes bounce {
      0%,100% { transform: translateX(-50%) translateY(0); }
      50% { transform: translateX(-50%) translateY(6px); }
    }

    @media (max-width: 1023px) {
      .hero-grid {
        grid-template-columns: 1fr;
        min-height: auto;
        padding-top: 80px;
        padding-bottom: 80px;
        gap: 48px;
        text-align: center;
      }
      .hero-content { max-width: 100%; margin: 0 auto; }
      .hero-sub { margin: 0 auto 40px; }
      .hero-ctas { justify-content: center; }
      .hero-badges { justify-content: center; }
      .hero-image-wrap { height: 400px; }
    }
    @media (max-width: 767px) {
      .hero-image-wrap { height: 280px; }
      .hero-image-card { display: none; }
    }

    /* ============================================================
       4. TRUST BAR
    ============================================================ */
    #trust-bar {
      background: var(--color-white);
      border-top: 1px solid var(--color-border);
      border-bottom: 1px solid var(--color-border);
      padding: 28px 0;
    }
    .trust-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      max-width: var(--max-width);
      margin: 0 auto;
      padding: 0 24px;
      flex-wrap: wrap;
    }
    .trust-stat {
      display: flex;
      align-items: center;
      gap: 12px;
      flex: 1;
      min-width: 160px;
      justify-content: center;
    }
    .trust-stat + .trust-stat {
      border-left: 1px solid var(--color-border);
    }
    .trust-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, rgba(45,80,22,0.08), rgba(200,151,58,0.08));
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .trust-text strong {
      display: block;
      font-size: 1rem;
      font-weight: 700;
      color: var(--color-primary);
    }
    .trust-text span {
      font-size: 0.75rem;
      color: var(--color-text-muted);
    }
    @media (max-width: 767px) {
      .trust-inner { gap: 8px; }
      .trust-stat { min-width: 130px; }
      .trust-stat + .trust-stat { border-left: none; border-top: 1px solid var(--color-border); padding-top: 8px; }
      .trust-inner { flex-direction: column; }
    }

    /* ============================================================
       5. PRODUCT CAROUSEL
    ============================================================ */
    #products {
      padding: 96px 0;
      background: var(--color-bg-alt);
      overflow: hidden;
    }
    .products-header {
      text-align: center;
      margin-bottom: 56px;
    }

    .carousel-outer {
      position: relative;
    }

    .carousel-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
      width: 52px;
      height: 52px;
      border-radius: 50%;
      background: var(--color-white);
      border: 2px solid var(--color-border);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: var(--shadow-md);
      transition: var(--transition);
      color: var(--color-primary);
    }
    .carousel-btn:hover {
      background: var(--color-primary);
      border-color: var(--color-primary);
      color: white;
      box-shadow: var(--shadow-lg);
    }
    .carousel-btn svg { width: 20px; height: 20px; }
    #carousel-prev { left: 16px; }
    #carousel-next { right: 16px; }

    .carousel-viewport {
      overflow: hidden;
      padding: 24px 0 40px;
      -webkit-mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
      mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
    }

    .carousel-track {
      display: flex;
      gap: 24px;
      transition: transform 0.5s cubic-bezier(0.4,0,0.2,1);
      will-change: transform;
    }

    .product-card {
      flex-shrink: 0;
      width: 280px;
      background: var(--color-white);
      border-radius: var(--radius-md);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: opacity 0.5s ease, transform 0.5s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }
    .product-card:hover { box-shadow: var(--shadow-lg); }
    .product-card.active { box-shadow: var(--shadow-lg); }

    .product-img-wrap {
      position: relative;
      height: 280px;
      overflow: hidden;
    }

    .product-img-placeholder {
      width: 100%;
      height: 100%;
      background: linear-gradient(160deg, var(--color-primary) 0%, var(--color-secondary) 60%, rgba(200,151,58,0.4) 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
      color: rgba(255,255,255,0.9);
      font-family: var(--font-heading);
    }

    /* Each product gets a slightly different gradient */
    .product-card:nth-child(1) .product-img-placeholder { background: linear-gradient(160deg,#2D5016,#4A7C2F,rgba(200,151,58,0.5)); }
    .product-card:nth-child(2) .product-img-placeholder { background: linear-gradient(160deg,#3D4E0A,#5A7C1A,rgba(200,151,58,0.4)); }
    .product-card:nth-child(3) .product-img-placeholder { background: linear-gradient(160deg,#2D5016,#3D6B1E,rgba(160,120,40,0.5)); }
    .product-card:nth-child(4) .product-img-placeholder { background: linear-gradient(160deg,#1A3A0A,#2D5016,rgba(200,151,58,0.6)); }
    .product-card:nth-child(5) .product-img-placeholder { background: linear-gradient(160deg,#3D5016,#5A7C2F,rgba(180,130,40,0.5)); }
    .product-card:nth-child(6) .product-img-placeholder { background: linear-gradient(160deg,#2D4016,#4A6B1E,rgba(200,151,58,0.7)); }
    .product-card:nth-child(7) .product-img-placeholder { background: linear-gradient(160deg,#2D5016,#3D6B1E,rgba(200,151,58,0.45)); }

    .product-img-placeholder .pip-label {
      font-size: 0.6875rem;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      opacity: 0.7;
    }
    .product-img-placeholder .pip-name {
      font-size: 1.125rem;
      font-weight: 600;
      text-align: center;
      padding: 0 16px;
    }

    /* Real product image (swap in when available) */
    .product-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
    }

    .product-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      background: var(--color-accent);
      color: white;
      font-size: 0.6875rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 50px;
    }

    .product-body {
      padding: 20px;
    }
    .product-name {
      font-family: var(--font-heading);
      font-size: 1.0625rem;
      font-weight: 600;
      color: var(--color-primary);
      margin-bottom: 6px;
      line-height: 1.3;
    }
    .product-desc {
      font-size: 0.8125rem;
      color: var(--color-text-muted);
      line-height: 1.5;
      margin-bottom: 12px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .product-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
    .product-price {
      font-weight: 700;
      font-size: 1rem;
      color: var(--color-primary);
    }
    .product-cta {
      display: block;
      width: 100%;
      text-align: center;
      padding: 10px 16px;
      background: var(--color-primary);
      color: white;
      border-radius: 50px;
      font-size: 0.8125rem;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: var(--transition);
    }
    .product-cta:hover { background: var(--color-secondary); }

    .carousel-dots {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 8px;
    }
    .carousel-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--color-border);
      border: none;
      cursor: pointer;
      transition: var(--transition);
      padding: 0;
    }
    .carousel-dot.active {
      background: var(--color-accent);
      width: 24px;
      border-radius: 4px;
    }

    @media (max-width: 767px) {
      .product-card { width: calc(100vw - 120px); min-width: 240px; }
    }
    @media (min-width: 768px) and (max-width: 1023px) {
      .product-card { width: 260px; }
    }

    /* ============================================================
       6. BENEFITS
    ============================================================ */
    #benefits {
      padding: 96px 0;
      background: var(--color-bg);
    }
    .benefits-header { text-align: center; }
    .benefits-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 32px;
      margin-top: 56px;
    }
    .benefit-card {
      background: var(--color-white);
      border-radius: var(--radius-md);
      padding: 36px 28px;
      border: 1px solid var(--color-border);
      transition: var(--transition);
    }
    .benefit-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-md);
      border-color: rgba(200,151,58,0.3);
    }
    .benefit-icon {
      width: 56px;
      height: 56px;
      background: linear-gradient(135deg, rgba(45,80,22,0.08), rgba(200,151,58,0.12));
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
    }
    .benefit-icon svg { width: 28px; height: 28px; color: var(--color-primary); }
    .benefit-title {
      font-family: var(--font-heading);
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--color-primary);
      margin-bottom: 10px;
    }
    .benefit-desc { font-size: 0.875rem; color: var(--color-text-muted); line-height: 1.65; }
    @media (max-width: 1023px) { .benefits-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 599px) { .benefits-grid { grid-template-columns: 1fr; } }

    /* ============================================================
       7. BRAND STORY
    ============================================================ */
    #story {
      padding: 96px 0;
      background: var(--color-bg-alt);
    }
    .story-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 64px;
      align-items: center;
      max-width: var(--max-width);
      margin: 0 auto;
      padding: 0 24px;
    }
    .story-image-wrap {
      border-radius: var(--radius-lg);
      overflow: hidden;
      height: 520px;
      position: relative;
    }
    .story-img { width:100%; height:100%; object-fit:cover; object-position: center top; }
    .story-img-placeholder {
      width:100%; height:100%;
      background: linear-gradient(160deg, #3D6B1E 0%, #2D5016 50%, #1A2E0A 100%);
      display:flex; align-items:center; justify-content:center;
      color:rgba(255,255,255,0.5); font-style:italic; font-size:0.875rem;
    }
    .story-content { padding-right: 24px; }
    .story-quote {
      font-family: var(--font-heading);
      font-size: clamp(1.375rem, 2.5vw, 1.875rem);
      font-style: italic;
      color: var(--color-primary);
      line-height: 1.4;
      margin-bottom: 28px;
      padding-left: 24px;
      border-left: 4px solid var(--color-accent);
    }
    .story-text {
      font-size: 1rem;
      color: var(--color-text-muted);
      line-height: 1.8;
      margin-bottom: 32px;
    }
    .story-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--color-accent-dark);
      font-weight: 600;
      font-size: 0.9375rem;
      text-decoration: none;
      border-bottom: 2px solid currentColor;
      padding-bottom: 2px;
      transition: var(--transition);
    }
    .story-link:hover { color: var(--color-primary); }
    @media (max-width: 1023px) {
      .story-grid { grid-template-columns: 1fr; }
      .story-image-wrap { height: 360px; }
      .story-content { padding-right: 0; }
    }

    /* ============================================================
       8. HOW IT WORKS
    ============================================================ */
    #how-it-works {
      padding: 96px 0;
      background: var(--color-bg);
    }
    .how-header { text-align: center; }
    .steps-grid {
      display: flex;
      gap: 0;
      justify-content: center;
      margin-top: 56px;
      position: relative;
    }
    .steps-grid::before {
      content: "";
      position: absolute;
      top: 52px;
      left: calc(16.66% + 52px);
      right: calc(16.66% + 52px);
      height: 2px;
      background: linear-gradient(to right, var(--color-accent), var(--color-primary));
    }
    .step {
      flex: 1;
      max-width: 320px;
      text-align: center;
      padding: 0 24px;
    }
    .step-num {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--color-accent), var(--color-accent-dark));
      color: white;
      font-family: var(--font-heading);
      font-size: 1.75rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      position: relative;
      z-index: 1;
      box-shadow: 0 8px 24px rgba(200,151,58,0.3);
    }
    .step-title {
      font-family: var(--font-heading);
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--color-primary);
      margin-bottom: 10px;
    }
    .step-desc { font-size: 0.875rem; color: var(--color-text-muted); line-height: 1.6; }
    @media (max-width: 767px) {
      .steps-grid { flex-direction: column; align-items: center; gap: 40px; }
      .steps-grid::before { display: none; }
      .step { padding: 0; }
    }

    /* ============================================================
       9. SCIENCE SECTION
    ============================================================ */
    #science {
      padding: 96px 0;
      background: var(--color-bg-dark);
      color: var(--color-white);
    }
    #science .section-title { color: var(--color-white); }
    #science .section-label { color: var(--color-accent-light); }
    .science-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 64px;
      align-items: start;
      margin-top: 48px;
    }
    .science-text p {
      color: rgba(255,255,255,0.75);
      line-height: 1.8;
      margin-bottom: 20px;
      font-size: 0.9375rem;
    }
    .science-disclaimer {
      font-size: 0.75rem;
      color: rgba(255,255,255,0.45);
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
      line-height: 1.6;
    }
    .fact-card {
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: var(--radius-md);
      padding: 36px;
      backdrop-filter: blur(8px);
    }
    .fact-card h3 {
      font-family: var(--font-heading);
      font-size: 1.125rem;
      color: var(--color-accent-light);
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .fact-row {
      display: flex;
      justify-content: space-between;
      padding: 14px 0;
      border-bottom: 1px solid rgba(255,255,255,0.07);
      font-size: 0.875rem;
    }
    .fact-row:last-child { border-bottom: none; }
    .fact-label { color: rgba(255,255,255,0.5); }
    .fact-value { color: var(--color-white); font-weight: 600; }
    @media (max-width: 1023px) { .science-grid { grid-template-columns: 1fr; gap: 40px; } }

    /* ============================================================
       10. UGC / TESTIMONIALS
    ============================================================ */
    #testimonials {
      padding: 96px 0;
      background: var(--color-bg-alt);
    }
    .testimonials-header { text-align: center; }
    .testimonial-cards {
      display: grid;
      grid-template-columns: repeat(3,1fr);
      gap: 24px;
      margin-top: 56px;
      margin-bottom: 56px;
    }
    .testimonial-card {
      background: var(--color-white);
      border-radius: var(--radius-md);
      padding: 28px;
      border: 1px solid var(--color-border);
      transition: var(--transition);
    }
    .testimonial-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .t-stars { margin-bottom: 12px; }
    .t-quote {
      font-size: 0.9375rem;
      color: var(--color-text);
      line-height: 1.75;
      margin-bottom: 20px;
      font-style: italic;
    }
    .t-author {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .t-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 0.875rem;
      flex-shrink: 0;
    }
    .t-name { font-weight: 600; font-size: 0.875rem; color: var(--color-primary); }
    .t-verified { font-size: 0.75rem; color: var(--color-success); }

    /* Video placeholders */
    .ugc-videos-heading {
      font-family: var(--font-heading);
      font-size: 1.375rem;
      color: var(--color-primary);
      text-align: center;
      margin-bottom: 32px;
    }
    .video-embeds {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-bottom: 56px;
    }
    .video-embed-wrap {
      border-radius: var(--radius-md);
      overflow: hidden;
    }
    .video-placeholder {
      aspect-ratio: 9/16;
      background: var(--color-bg-dark);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 16px;
      cursor: pointer;
      transition: var(--transition);
      position: relative;
      max-height: 480px;
    }
    .video-placeholder:hover { opacity: 0.9; }
    .video-play-btn {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      background: rgba(200,151,58,0.9);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .video-play-btn svg { width: 24px; height: 24px; color: white; margin-left: 4px; }
    .video-label {
      color: rgba(255,255,255,0.7);
      font-size: 0.875rem;
      text-align: center;
      padding: 0 16px;
    }
    .video-social-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.2);
      color: white;
      font-size: 0.6875rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 50px;
    }
    /* Real embed container */
    .social-embed-container { border-radius: var(--radius-md); overflow: hidden; background: var(--color-white); }
    .embed-placeholder-note {
      text-align: center;
      font-size: 0.8125rem;
      color: var(--color-text-light);
      padding: 16px;
      background: rgba(45,80,22,0.04);
      border-radius: 0 0 var(--radius-md) var(--radius-md);
    }

    /* UGC Photo Grid */
    .ugc-photos-heading {
      font-family: var(--font-heading);
      font-size: 1.375rem;
      color: var(--color-primary);
      text-align: center;
      margin-bottom: 32px;
    }
    .ugc-photo-grid {
      display: grid;
      grid-template-columns: repeat(3,1fr);
      gap: 16px;
      margin-bottom: 24px;
    }
    .ugc-photo {
      aspect-ratio: 1;
      background: var(--color-bg);
      border-radius: var(--radius-sm);
      border: 2px dashed var(--color-border);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
      color: var(--color-text-light);
      font-size: 0.75rem;
      cursor: pointer;
      transition: var(--transition);
    }
    .ugc-photo:hover { border-color: var(--color-accent); color: var(--color-accent); }
    .ugc-photo svg { width: 24px; height: 24px; opacity: 0.4; }
    .ugc-coming-soon {
      text-align: center;
      font-size: 0.875rem;
      color: var(--color-text-muted);
      font-style: italic;
      margin-bottom: 32px;
    }
    .t-disclaimer {
      text-align: center;
      font-size: 0.75rem;
      color: var(--color-text-light);
      margin-top: 16px;
    }
    @media (max-width: 1023px) {
      .testimonial-cards { grid-template-columns: 1fr; }
      .video-embeds { grid-template-columns: 1fr; }
    }
    @media (max-width: 767px) {
      .ugc-photo-grid { grid-template-columns: repeat(2,1fr); }
    }

    /* ============================================================
       11. FAQ
    ============================================================ */
    #faq {
      padding: 96px 0;
      background: var(--color-bg);
    }
    .faq-header { text-align: center; }
    .faq-list {
      max-width: 800px;
      margin: 56px auto 0;
    }
    .faq-item {
      border-bottom: 1px solid var(--color-border);
    }
    .faq-question {
      width: 100%;
      text-align: left;
      background: none;
      border: none;
      padding: 24px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      cursor: pointer;
      font-family: var(--font-body);
      font-size: 1rem;
      font-weight: 600;
      color: var(--color-text);
      transition: color 0.2s;
    }
    .faq-question:hover { color: var(--color-primary); }
    .faq-question.open { color: var(--color-primary); }
    .faq-icon {
      flex-shrink: 0;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: var(--color-bg-alt);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
    }
    .faq-question.open .faq-icon {
      background: var(--color-primary);
      transform: rotate(45deg);
    }
    .faq-question.open .faq-icon svg { color: white; }
    .faq-icon svg { width: 16px; height: 16px; color: var(--color-text-muted); }
    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease, padding 0.3s ease;
    }
    .faq-answer.open { max-height: 400px; padding-bottom: 24px; }
    .faq-answer p {
      font-size: 0.9375rem;
      color: var(--color-text-muted);
      line-height: 1.8;
    }

    /* ============================================================
       12. FINAL CTA BANNER
    ============================================================ */
    #cta-banner {
      padding: 100px 0;
      background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 60%, #3A6B25 100%);
      position: relative;
      overflow: hidden;
      text-align: center;
    }
    #cta-banner::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 70% 50%, rgba(200,151,58,0.2) 0%, transparent 60%);
      pointer-events: none;
    }
    .cta-title {
      font-family: var(--font-heading);
      font-size: clamp(2rem, 4vw, 3.25rem);
      color: var(--color-white);
      margin-bottom: 16px;
      position: relative;
      z-index: 1;
    }
    .cta-sub {
      font-size: 1.0625rem;
      color: rgba(255,255,255,0.8);
      margin-bottom: 40px;
      position: relative;
      z-index: 1;
    }
    .cta-actions { position: relative; z-index: 1; display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
    .trust-seals {
      display: flex;
      justify-content: center;
      gap: 32px;
      margin-top: 40px;
      flex-wrap: wrap;
      position: relative;
      z-index: 1;
    }
    .seal {
      display: flex;
      align-items: center;
      gap: 8px;
      color: rgba(255,255,255,0.7);
      font-size: 0.8125rem;
      font-weight: 500;
    }
    .seal svg { width: 20px; height: 20px; }

    /* ============================================================
       13. FOOTER
    ============================================================ */
    #site-footer {
      background: var(--color-bg-dark);
      color: rgba(255,255,255,0.65);
      padding: 80px 0 0;
    }
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 48px;
      padding-bottom: 64px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .footer-brand-logo {
      font-family: var(--font-heading);
      font-size: 1.875rem;
      font-weight: 700;
      color: var(--color-white);
      margin-bottom: 8px;
    }
    .footer-brand-sub {
      font-size: 0.6875rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: rgba(255,255,255,0.4);
      margin-bottom: 16px;
    }
    .footer-tagline {
      font-size: 0.875rem;
      line-height: 1.7;
      margin-bottom: 24px;
    }
    .social-icons {
      display: flex;
      gap: 12px;
    }
    .social-icon {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
      color: rgba(255,255,255,0.6);
    }
    .social-icon:hover {
      background: var(--color-accent);
      border-color: var(--color-accent);
      color: white;
    }
    .social-icon svg { width: 16px; height: 16px; }
    .footer-col h4 {
      font-family: var(--font-heading);
      font-size: 1rem;
      font-weight: 600;
      color: var(--color-white);
      margin-bottom: 20px;
    }
    .footer-links {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .footer-links a {
      font-size: 0.875rem;
      color: rgba(255,255,255,0.55);
      transition: color 0.2s;
    }
    .footer-links a:hover { color: var(--color-accent-light); }
    .footer-bottom {
      padding: 24px 0;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 24px;
      flex-wrap: wrap;
    }
    .footer-copy {
      font-size: 0.8125rem;
      color: rgba(255,255,255,0.35);
    }
    .footer-legal {
      display: flex;
      gap: 20px;
      font-size: 0.75rem;
    }
    .footer-legal a { color: rgba(255,255,255,0.35); }
    .footer-legal a:hover { color: rgba(255,255,255,0.6); }
    .footer-disclaimer {
      width: 100%;
      font-size: 0.6875rem;
      color: rgba(255,255,255,0.3);
      line-height: 1.7;
      border-top: 1px solid rgba(255,255,255,0.06);
      padding-top: 20px;
      margin-top: 8px;
    }
    @media (max-width: 1023px) {
      .footer-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 599px) {
      .footer-grid { grid-template-columns: 1fr; gap: 36px; }
      .footer-bottom { flex-direction: column; gap: 12px; }
    }
  
/* WordPress overrides */
.fenben-landing .entry-content,
.fenben-landing .site-content,
.fenben-landing #primary { max-width: none !important; padding: 0 !important; margin: 0 !important; }
.fenben-landing #site-header { display: none; }
</style>


<?php // ================================================================ ?>
<?php // ANNOUNCEMENT BAR                                                 ?>
<?php // ================================================================ ?>
<div id="announcement-bar" role="banner">
    Free Shipping on Orders Over $75 &nbsp;|&nbsp;
    Use Code <strong>HEAL10</strong> for 10% Off Your First Order
    <a href="<?php echo esc_url( function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop') ); ?>">Shop Now &rarr;</a>
    <button class="announce-close" onclick="document.getElementById('announcement-bar').style.display='none'" aria-label="Dismiss">&times;</button>
</div>

<?php // ================================================================ ?>
<?php // NAVIGATION                                                        ?>
<?php // ================================================================ ?>
<nav id="site-nav" role="navigation" aria-label="Main navigation">
    <div class="nav-inner">
        <a href="<?php echo esc_url( home_url() ); ?>" class="nav-logo" aria-label="Fenben by The Happy Healing Store">
            <span class="nav-logo-main">Fenben&#174;</span>
            <span class="nav-logo-sub">by The Happy Healing Store</span>
        </a>
        <ul class="nav-links" role="list">
            <li><a href="#products">Our Products</a></li>
            <li><a href="#science">The Science</a></li>
            <li><a href="#testimonials">Testimonials</a></li>
            <li><a href="#faq">FAQ</a></li>
            <li><a href="<?php echo esc_url( function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop') ); ?>">Shop All</a></li>
        </ul>
        <div class="nav-cta">
            <a href="<?php echo esc_url( function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop') ); ?>" class="nav-shop-btn">Shop Now</a>
            <button class="hamburger" id="hamburger-btn" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>
<div id="mobile-menu" role="dialog" aria-label="Mobile navigation" aria-modal="true">
    <a href="#products" onclick="closeMobileMenu()">Our Products</a>
    <a href="#science" onclick="closeMobileMenu()">The Science</a>
    <a href="#testimonials" onclick="closeMobileMenu()">Testimonials</a>
    <a href="#faq" onclick="closeMobileMenu()">FAQ</a>
    <a href="<?php echo esc_url( function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop') ); ?>" onclick="closeMobileMenu()" style="color:var(--color-accent)">Shop All &rarr;</a>
</div>

<main id="main-content">

<?php // ================================================================ ?>
<?php // HERO                                                              ?>
<?php // ================================================================ ?>
<section id="hero" aria-label="Hero">
    <div class="hero-grid container">
        <div class="hero-content fade-in-up">
            <span class="hero-eyebrow">&#9733; Trusted by 50,000+ Wellness Seekers</span>
            <h1 class="hero-title">Your Journey Back to<br><em>Vitality</em> Starts Here</h1>
            <p class="hero-sub">Fenben&#174; by The Happy Healing Store &mdash; premium-grade wellness supplements crafted with pharmaceutical purity for those who refuse to settle for less.</p>
            <div class="hero-ctas">
                <a href="#products" class="btn btn-primary">Shop Fenben&#174; Products</a>
                <a href="#story" class="btn btn-outline">Learn More</a>
            </div>
            <div class="hero-badges">
                <span class="hero-badge">Lab Tested</span>
                <span class="hero-badge">USA Formulated</span>
                <span class="hero-badge">30-Day Guarantee</span>
            </div>
        </div>
        <div class="hero-image-wrap fade-in-up">
            <?php
            $hero_img = content_url( '/uploads/fenben/fenben-hero-couple-golden-field.jpg' );
            ?>
            <img src="<?php echo esc_url( $hero_img ); ?>"
                 alt="Happy couple walking through golden fields at sunset &mdash; Fenben&#174; wellness"
                 class="hero-img" loading="eager"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="hero-img-placeholder">
                <span>Hero Image &mdash; Upload to: /wp-content/uploads/fenben/fenben-hero-couple-golden-field.jpg</span>
            </div>
            <div class="hero-image-card">
                <div class="hero-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="hero-card-text">
                    <strong>Third-Party Tested</strong>
                    Every batch verified for purity
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <span>Scroll</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
    </div>
</section>

<?php // ================================================================ ?>
<?php // TRUST BAR                                                         ?>
<?php // ================================================================ ?>
<section id="trust-bar" aria-label="Trust indicators">
    <div class="trust-inner">
        <div class="trust-stat"><div class="trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2D5016" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div><div class="trust-text"><strong>50,000+</strong><span>Happy Customers</span></div></div>
        <div class="trust-stat"><div class="trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="#C8973A" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div><div class="trust-text"><strong>4.9&#9733; Rating</strong><span>Average Review Score</span></div></div>
        <div class="trust-stat"><div class="trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2D5016" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg></div><div class="trust-text"><strong>7+ Formulas</strong><span>Product Varieties</span></div></div>
        <div class="trust-stat"><div class="trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2D5016" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><div class="trust-text"><strong>100% Pure</strong><span>No Fillers</span></div></div>
        <div class="trust-stat"><div class="trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2D5016" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><div class="trust-text"><strong>30-Day</strong><span>Money-Back Guarantee</span></div></div>
    </div>
</section>

<?php // ================================================================ ?>
<?php // PRODUCT CAROUSEL                                                  ?>
<?php // ================================================================ ?>
<section id="products" aria-label="Product collection">
    <div class="products-header container fade-in-up">
        <p class="section-label">The Collection</p>
        <h2 class="section-title">The Fenben&#174; Collection</h2>
        <p class="section-subtitle">Seven powerful formulas. One mission &mdash; your wellbeing.</p>
    </div>
    <div class="carousel-outer" role="region" aria-label="Product carousel" aria-roledescription="carousel">
        <button class="carousel-btn" id="carousel-prev" aria-label="Previous product">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="carousel-viewport">
            <div class="carousel-track" id="carousel-track">
                <?php
                global $fenben_products;
                foreach ( $fenben_products as $idx => $product ) {
                    fenben_render_card( $product, $idx );
                }
                ?>
            </div>
        </div>
        <button class="carousel-btn" id="carousel-next" aria-label="Next product">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
    <div class="carousel-dots" id="carousel-dots" role="tablist">
        <?php for ( $i = 0; $i < count( $fenben_products ); $i++ ) : ?>
        <button class="carousel-dot <?php echo $i === 0 ? 'active' : ''; ?>"
                role="tab" data-dot="<?php echo $i; ?>"
                aria-label="Product <?php echo $i + 1; ?>"
                aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"></button>
        <?php endfor; ?>
    </div>
</section>

<?php // ================================================================ ?>
<?php // BENEFITS                                                          ?>
<?php // ================================================================ ?>
<section id="benefits" aria-label="Why Fenben">
    <div class="container">
        <div class="benefits-header fade-in-up">
            <p class="section-label">Why Choose Us</p>
            <h2 class="section-title">Why Thousands Choose Fenben&#174;</h2>
        </div>
        <div class="benefits-grid">
            <div class="benefit-card fade-in-up"><div class="benefit-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3 class="benefit-title">Pure Formulation</h3><p class="benefit-desc">No fillers, no additives &mdash; just pharmaceutical-grade purity you can trust in every dose.</p></div>
            <div class="benefit-card fade-in-up"><div class="benefit-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div><h3 class="benefit-title">Lab Verified</h3><p class="benefit-desc">Every batch is third-party tested for potency, purity, and safety before it reaches your hands.</p></div>
            <div class="benefit-card fade-in-up"><div class="benefit-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg></div><h3 class="benefit-title">Multiple Formats</h3><p class="benefit-desc">Powder, capsules, tablets &mdash; choose the format and strength that fits your lifestyle and protocol.</p></div>
            <div class="benefit-card fade-in-up"><div class="benefit-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div><h3 class="benefit-title">Trusted Source</h3><p class="benefit-desc">Proudly brought to you by The Happy Healing Store &mdash; a trusted name in wellness since 2017.</p></div>
            <div class="benefit-card fade-in-up"><div class="benefit-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg></div><h3 class="benefit-title">USA Formulated</h3><p class="benefit-desc">Manufactured in cGMP-certified USA facilities to the highest pharmaceutical standards.</p></div>
            <div class="benefit-card fade-in-up"><div class="benefit-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></div><h3 class="benefit-title">Real Results</h3><p class="benefit-desc">Backed by thousands of verified customer testimonials from people on real wellness journeys.</p></div>
        </div>
    </div>
</section>


<?php // ================================================================ ?>
<?php // 6b. BRAND CREDENTIALS (Trust Imagery)                            ?>
<?php // ================================================================ ?>
<section id="brand-credentials" aria-label="Brand trust credentials">
    <div class="credentials-grid">
        <div class="credential-card">
            <?php $img1 = content_url( '/uploads/fenben/fenben-brand-guaranteed-purity.jpg' ); ?>
            <img src="<?php echo esc_url( $img1 ); ?>"
                 alt="Fenben&#174; — Guaranteed Purity. Trusted USA Source. Precision Formulation. Happy Healing Trusted Brand."
                 class="credential-img" loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="credential-placeholder" style="display:none">
                <strong>Guaranteed Purity</strong>
                <span>Upload: /wp-content/uploads/fenben/fenben-brand-guaranteed-purity.jpg</span>
            </div>
            <div class="credential-overlay"></div>
        </div>
        <div class="credential-card">
            <?php $img2 = content_url( '/uploads/fenben/fenben-brand-independently-tested.jpg' ); ?>
            <img src="<?php echo esc_url( $img2 ); ?>"
                 alt="Fenben&#174; — Independently Tested. Guaranteed purity and free from contaminants."
                 class="credential-img" loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="credential-placeholder" style="display:none">
                <strong>Independently Tested</strong>
                <span>Upload: /wp-content/uploads/fenben/fenben-brand-independently-tested.jpg</span>
            </div>
            <div class="credential-overlay"></div>
        </div>
        <div class="credential-card">
            <?php $img3 = content_url( '/uploads/fenben/fenben-brand-100-pure.jpg' ); ?>
            <img src="<?php echo esc_url( $img3 ); ?>"
                 alt="Fenben&#174; Pure Powder — 100% Pure Fenbendazole. 225 scoops at 222mg per scoop. Happy Healing since 2017."
                 class="credential-img" loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="credential-placeholder" style="display:none">
                <strong>100% Pure Fenbendazole</strong>
                <span>Upload: /wp-content/uploads/fenben/fenben-brand-100-pure.jpg</span>
            </div>
            <div class="credential-overlay"></div>
        </div>
    </div>
</section>

<?php // ================================================================ ?>
<?php // BRAND STORY                                                       ?>
<?php // ================================================================ ?>
<section id="story" aria-label="Our brand story">
    <div class="story-grid">
        <div class="story-image-wrap fade-in-up">
            <?php $story_img = content_url( '/uploads/fenben/fenben-story-woman-hiking.jpg' ); ?>
            <img src="<?php echo esc_url( $story_img ); ?>"
                 alt="Woman hiking mountain trail at sunrise &mdash; Fenben&#174; natural wellness"
                 class="story-img" loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="story-img-placeholder">Upload: /wp-content/uploads/fenben/fenben-story-woman-hiking.jpg</div>
        </div>
        <div class="story-content fade-in-up">
            <p class="section-label">Our Story</p>
            <blockquote class="story-quote">&ldquo;We believe in nature&rsquo;s wisdom &mdash; and your right to access it.&rdquo;</blockquote>
            <p class="story-text">The Happy Healing Store was founded in 2017 with one mission: to make effective, pharmaceutical-grade wellness supplements accessible to everyone. We saw too many people searching for alternatives &mdash; people who deserved better options and better information.</p>
            <p class="story-text">Fenben&#174; was born from that mission. We spent years sourcing the purest fenbendazole available, developing multiple formats to suit every lifestyle, and building a community of wellness seekers. Every product we make carries our commitment to purity, transparency, and your wellbeing.</p>
            <a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="story-link">Read Our Full Story <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        </div>
    </div>
</section>

<?php // ================================================================ ?>
<?php // HOW IT WORKS                                                      ?>
<?php // ================================================================ ?>
<section id="how-it-works" aria-label="How to start">
    <div class="container">
        <div class="how-header fade-in-up">
            <p class="section-label">Simple Steps</p>
            <h2 class="section-title">Starting Your Fenben&#174; Journey</h2>
            <p class="section-subtitle">Three simple steps to begin your wellness protocol.</p>
        </div>
        <div class="steps-grid">
            <div class="step fade-in-up"><div class="step-num">1</div><h3 class="step-title">Choose Your Formula</h3><p class="step-desc">Browse our 7 Fenben&#174; formulations &mdash; from pure powder to precise-dose tablets &mdash; and find the format that fits your lifestyle.</p></div>
            <div class="step fade-in-up" style="animation-delay:.15s"><div class="step-num">2</div><h3 class="step-title">Begin Your Protocol</h3><p class="step-desc">Follow our simple dosing guide. Each Fenben&#174; product comes with clear instructions to support your wellness journey.</p></div>
            <div class="step fade-in-up" style="animation-delay:.3s"><div class="step-num">3</div><h3 class="step-title">Feel the Difference</h3><p class="step-desc">Track your wellness journey and join over 50,000 customers who have made Fenben&#174; a part of their daily health routine.</p></div>
        </div>
    </div>
</section>

<?php // ================================================================ ?>
<?php // SCIENCE                                                           ?>
<?php // ================================================================ ?>
<section id="science" aria-label="The science">
    <div class="container">
        <p class="section-label">Research &amp; Purity</p>
        <h2 class="section-title">The Science Behind Fenben&#174;</h2>
        <div class="science-grid">
            <div class="science-text">
                <p>Fenbendazole is a benzimidazole compound with decades of documented use and an extensive safety profile. Originally developed for veterinary applications, it has become the subject of significant scientific interest in human wellness contexts.</p>
                <p>At Fenben&#174; by The Happy Healing Store, we source only pharmaceutical-grade fenbendazole with verified purity levels of 99%+. Unlike lesser products, every batch undergoes rigorous third-party analytical testing before production.</p>
                <p>We offer multiple formulation formats &mdash; pure powder for maximum flexibility, precisely dosed tablets at 222mg, 500mg, and 750mg strengths, the Fenben&#174; Trio 694mg blend, plus enhanced bioavailability Bio Capsules &mdash; to ensure every customer finds their ideal protocol.</p>
                <p class="science-disclaimer">&#42; These statements have not been evaluated by the Food and Drug Administration. Fenben&#174; products are not intended to diagnose, treat, cure, or prevent any disease. Consult with a qualified healthcare professional before beginning any supplement regimen.</p>
            </div>
            <div class="fact-card">
                <h3>Ingredient Profile</h3>
                <div class="fact-row"><span class="fact-label">Active Ingredient</span><span class="fact-value">Fenbendazole</span></div>
                <div class="fact-row"><span class="fact-label">Purity Grade</span><span class="fact-value">Pharmaceutical (99%+)</span></div>
                <div class="fact-row"><span class="fact-label">Testing Protocol</span><span class="fact-value">Third-Party Verified</span></div>
                <div class="fact-row"><span class="fact-label">Origin</span><span class="fact-value">USA Manufactured</span></div>
                <div class="fact-row"><span class="fact-label">Facility Standard</span><span class="fact-value">cGMP Certified</span></div>
                <div class="fact-row"><span class="fact-label">Available Formats</span><span class="fact-value">Powder, Capsules, Tablets</span></div>
                <div class="fact-row"><span class="fact-label">Available Strengths</span><span class="fact-value">222mg / 500mg / 694mg / 750mg</span></div>
                <div class="fact-row"><span class="fact-label">Brand Since</span><span class="fact-value">2017</span></div>
            </div>
        </div>
    </div>
</section>

<?php // ================================================================ ?>
<?php // UGC TESTIMONIALS                                                  ?>
<?php // ================================================================ ?>
<section id="testimonials" aria-label="Customer testimonials">
    <div class="container">
        <div class="testimonials-header fade-in-up">
            <p class="section-label">Community</p>
            <h2 class="section-title">Real People. Real Journeys.</h2>
            <p class="section-subtitle">Hear from members of our Fenben&#174; community.</p>
        </div>
        <div class="testimonial-cards">
            <article class="testimonial-card fade-in-up">
                <div class="t-stars stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="t-quote">&ldquo;I&rsquo;ve been using Fenben&#174; Pure Powder for 6 months and I feel like a completely different person. The quality is unmatched &mdash; you can tell this is a premium product.&rdquo;</p>
                <div class="t-author"><div class="t-avatar">SM</div><div><div class="t-name">Sarah M.</div><div class="t-verified">&#10003; Verified Buyer</div></div></div>
            </article>
            <article class="testimonial-card fade-in-up" style="animation-delay:.1s">
                <div class="t-stars stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="t-quote">&ldquo;The Trio Bundle was the best value I&rsquo;ve found anywhere. My whole family uses Fenben&#174; now. Fast shipping, beautiful packaging, and clearly the real thing.&rdquo;</p>
                <div class="t-author"><div class="t-avatar">RK</div><div><div class="t-name">Robert K.</div><div class="t-verified">&#10003; Verified Buyer</div></div></div>
            </article>
            <article class="testimonial-card fade-in-up" style="animation-delay:.2s">
                <div class="t-stars stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="t-quote">&ldquo;After trying other brands, Fenben&#174; Bio Capsules are clearly in a different league. The bioavailability difference is real and noticeable. This is my go-to now.&rdquo;</p>
                <div class="t-author"><div class="t-avatar">JL</div><div><div class="t-name">Jennifer L.</div><div class="t-verified">&#10003; Verified Buyer</div></div></div>
            </article>
        </div>

        <h3 class="ugc-videos-heading">Watch Community Stories</h3>
        <div class="video-embeds">
            <div class="video-embed-wrap">
                <div class="social-embed-container">
                    <!--
                    SWAP IN: Paste your Instagram embed code here, e.g.:
                    <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/YOUR_ID/">
                    </blockquote>
                    <script async src="//www.instagram.com/embed.js"></script>
                    OR TikTok:
                    <blockquote class="tiktok-embed" cite="https://www.tiktok.com/@user/video/ID">
                    </blockquote>
                    <script async src="https://www.tiktok.com/embed.js"></script>
                    -->
                    <div class="video-placeholder">
                        <span class="video-social-badge">Instagram / TikTok</span>
                        <div class="video-play-btn"><svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>
                        <p class="video-label">Customer Video Story #1<br><small>Paste embed code above</small></p>
                    </div>
                </div>
                <p class="embed-placeholder-note">Instagram / TikTok embed &mdash; replace placeholder with embed code</p>
            </div>
            <div class="video-embed-wrap">
                <div class="social-embed-container">
                    <!-- SWAP IN: Second video embed code here -->
                    <div class="video-placeholder">
                        <span class="video-social-badge">Instagram / TikTok</span>
                        <div class="video-play-btn"><svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>
                        <p class="video-label">Customer Video Story #2<br><small>Paste embed code above</small></p>
                    </div>
                </div>
                <p class="embed-placeholder-note">Instagram / TikTok embed &mdash; replace placeholder with embed code</p>
            </div>
        </div>

        <h3 class="ugc-photos-heading">From Our Community</h3>
        <div class="ugc-photo-grid">
            <?php for ( $i = 1; $i <= 6; $i++ ) : ?>
            <div class="ugc-photo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <span>UGC Photo <?php echo $i; ?></span>
            </div>
            <?php endfor; ?>
        </div>
        <p class="ugc-coming-soon">Influencer &amp; community photos coming soon.</p>
        <p class="t-disclaimer">&#42; Individual results may vary.</p>
    </div>
</section>

<?php // ================================================================ ?>
<?php // FAQ                                                               ?>
<?php // ================================================================ ?>
<section id="faq" aria-label="Frequently asked questions">
    <div class="container">
        <div class="faq-header fade-in-up">
            <p class="section-label">Got Questions?</p>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Everything you need to know about Fenben&#174; by The Happy Healing Store.</p>
        </div>
        <div class="faq-list" id="faq-list">
            <div class="faq-item"><button class="faq-question" aria-expanded="false">What is Fenben&#174; and how does it work?<span class="faq-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-answer"><p>Fenben&#174; contains fenbendazole, a benzimidazole compound with an extensive history of use and a well-documented safety profile. Our products are formulated to pharmaceutical-grade standards and undergo third-party testing before reaching you.</p></div></div>
            <div class="faq-item"><button class="faq-question" aria-expanded="false">What&rsquo;s the difference between Fenben&#174; product formats?<span class="faq-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-answer"><p>We offer <strong>Pure Powder</strong> (50g, flexible dosing), <strong>Tablets</strong> at 222mg, 500mg, and 750mg strengths, the <strong>Fenben&#174; Trio</strong> (694mg blend), <strong>Bio Capsules</strong> (enhanced bioavailability), and <strong>Pure Capsules</strong>. The <strong>Trio Bundle</strong> product page offers multi-item bundles.</p></div></div>
            <div class="faq-item"><button class="faq-question" aria-expanded="false">How do I choose the right Fenben&#174; product for me?<span class="faq-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-answer"><p>For those new to Fenben&#174;, we often recommend starting with our 222mg Tablets for ease of use and precise dosing. If you prefer flexible dosing, our Pure Powder is excellent. For enhanced absorption, try Bio Capsules. We always recommend consulting with a qualified healthcare professional first.</p></div></div>
            <div class="faq-item"><button class="faq-question" aria-expanded="false">Are Fenben&#174; products safe for daily use?<span class="faq-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-answer"><p>Our Fenben&#174; products are formulated to pharmaceutical-grade standards and manufactured in cGMP-certified facilities. Each batch is third-party tested. We strongly recommend consulting with a healthcare professional before starting, particularly if you have pre-existing conditions or take medications.</p></div></div>
            <div class="faq-item"><button class="faq-question" aria-expanded="false">What is your return policy?<span class="faq-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-answer"><p>We offer a <strong>30-day money-back guarantee</strong> on all Fenben&#174; products. If you&rsquo;re not completely satisfied for any reason, contact our customer care team within 30 days of delivery for a full refund. Your satisfaction is our priority.</p></div></div>
            <div class="faq-item"><button class="faq-question" aria-expanded="false">How long does shipping take?<span class="faq-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button><div class="faq-answer"><p>All Fenben&#174; orders ship within 1&ndash;2 business days from our USA warehouse. Standard shipping arrives in 3&ndash;5 business days. Free shipping on orders over $75. Expedited options available at checkout. You&rsquo;ll receive a tracking number by email when your order ships.</p></div></div>
        </div>
    </div>
</section>

<?php // ================================================================ ?>
<?php // FINAL CTA BANNER                                                  ?>
<?php // ================================================================ ?>
<section id="cta-banner" aria-label="Call to action">
    <div class="container">
        <h2 class="cta-title fade-in-up">Ready to Reclaim Your Health?</h2>
        <p class="cta-sub fade-in-up">Join over 50,000 people who&rsquo;ve chosen Fenben&#174; by The Happy Healing Store.</p>
        <div class="cta-actions fade-in-up">
            <a href="<?php echo esc_url( function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop') ); ?>" class="btn btn-accent">Shop All Fenben&#174; Products &rarr;</a>
            <a href="#faq" class="btn btn-outline-white">Read FAQs</a>
        </div>
        <div class="trust-seals">
            <div class="seal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg> SSL Secure Checkout</div>
            <div class="seal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> 30-Day Money Back</div>
            <div class="seal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4a1 1 0 011 1v5a1 1 0 01-1 1h-1"/><polyline points="12 17 9 20 6 17"/><line x1="9" y1="20" x2="9" y2="11"/></svg> Free Shipping $75+</div>
        </div>
    </div>
</section>

</main>

<?php // ================================================================ ?>
<?php // FOOTER                                                            ?>
<?php // ================================================================ ?>
<footer id="site-footer" aria-label="Site footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-brand-logo">Fenben&#174;</div>
                <div class="footer-brand-sub">by The Happy Healing Store</div>
                <p class="footer-tagline">Premium-grade fenbendazole supplements. Lab-tested, USA-formulated, trusted since 2017.</p>
                <div class="social-icons">
                    <a href="https://facebook.com/thehappyhealingstore" class="social-icon" aria-label="Facebook" rel="noopener noreferrer"><svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
                    <a href="https://instagram.com/thehappyhealingstore" class="social-icon" aria-label="Instagram" rel="noopener noreferrer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                    <a href="https://youtube.com/@thehappyhealingstore" class="social-icon" aria-label="YouTube" rel="noopener noreferrer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg></a>
                    <a href="https://tiktok.com/@thehappyhealingstore" class="social-icon" aria-label="TikTok" rel="noopener noreferrer"><svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.21 8.21 0 004.79 1.52V6.78a4.85 4.85 0 01-1.02-.09z"/></svg></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Products</h4>
                <ul class="footer-links">
                    <?php foreach ( $fenben_products as $p ) : ?>
                    <li><a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo esc_html( $p['name'] ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Information</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>">Our Story</a></li>
                    <li><a href="#science">The Science</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/blog' ) ); ?>">Blog</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Customer Care</h4>
                <ul class="footer-links">
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shipping-policy' ) ); ?>">Shipping Policy</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/refund_returns' ) ); ?>">Returns &amp; Refunds</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/terms-and-conditions' ) ); ?>">Terms &amp; Conditions</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="footer-copy">&copy; <?php echo date('Y'); ?> The Happy Healing Store. All rights reserved.</p>
            <div class="footer-legal">
                <a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>">Privacy</a>
                <a href="<?php echo esc_url( home_url( '/terms-and-conditions' ) ); ?>">Terms</a>
                <a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>">Sitemap</a>
            </div>
            <p class="footer-disclaimer">These statements have not been evaluated by the Food and Drug Administration. Fenben&#174; products are not intended to diagnose, treat, cure, or prevent any disease. Individual results may vary. Always consult with a qualified healthcare professional before beginning any new supplement regimen. Fenben&#174; is a registered trademark of The Happy Healing Store.</p>
        </div>
    </div>
</footer>

<script>

    // ============================================================
    //  FENBEN LANDING PAGE — JavaScript
    // ============================================================

    // ---- 1. Announcement Bar dismiss ----
    // (handled inline via onclick)

    // ---- 2. Mobile Menu ----
    const hamburger = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    function openMobileMenu() {
      mobileMenu.classList.add('open');
      hamburger.classList.add('open');
      hamburger.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
      mobileMenu.classList.remove('open');
      hamburger.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', () => {
      if (mobileMenu.classList.contains('open')) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    });

    // Close mobile menu on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMobileMenu();
    });

    // ---- 3. Sticky Nav ----
    const nav = document.getElementById('site-nav');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 60) {
        nav.classList.add('scrolled');
      } else {
        nav.classList.remove('scrolled');
      }
    }, { passive: true });

    // ---- 4. PRODUCT CAROUSEL ----
    (function() {
      const track = document.getElementById('carousel-track');
      const prevBtn = document.getElementById('carousel-prev');
      const nextBtn = document.getElementById('carousel-next');
      const dots = document.querySelectorAll('.carousel-dot');
      const cards = document.querySelectorAll('.product-card');
      const totalCards = cards.length;

      let currentIndex = 0;
      let autoPlayTimer = null;
      let startX = 0;
      let isDragging = false;

      // Get responsive visible count
      function getVisibleCount() {
        const w = window.innerWidth;
        if (w >= 1024) return 5;
        if (w >= 768) return 3;
        return 1;
      }

      // Get card width + gap
      function getCardStep() {
        const card = cards[0];
        if (!card) return 300;
        const style = window.getComputedStyle(track);
        const gap = parseFloat(style.gap) || 24;
        return card.offsetWidth + gap;
      }

      // Calculate translateX to center the active card
      function getTrackOffset(idx) {
        const viewport = document.querySelector('.carousel-viewport');
        const vpWidth = viewport ? viewport.offsetWidth : window.innerWidth;
        const cardStep = getCardStep();
        const cardWidth = cards[0] ? cards[0].offsetWidth : 280;
        const center = vpWidth / 2 - cardWidth / 2;
        return center - idx * cardStep;
      }

      // Apply opacity/scale to each card based on distance from center
      function updateCardStyles(idx) {
        cards.forEach((card, i) => {
          const dist = Math.abs(i - idx);
          let opacity, scale;
          if (dist === 0) { opacity = 1.0; scale = 1.0; }
          else if (dist === 1) { opacity = 0.72; scale = 0.95; }
          else { opacity = 0.35; scale = 0.85; }
          card.style.opacity = opacity;
          card.style.transform = 'scale(' + scale + ')';
          card.classList.toggle('active', i === idx);
          card.setAttribute('aria-hidden', i !== idx ? 'true' : 'false');
        });
      }

      // Move carousel to index
      function goTo(idx) {
        // Clamp index
        currentIndex = Math.max(0, Math.min(idx, totalCards - 1));
        const offset = getTrackOffset(currentIndex);
        track.style.transform = 'translateX(' + offset + 'px)';
        updateCardStyles(currentIndex);

        // Update dots
        dots.forEach((dot, i) => {
          dot.classList.toggle('active', i === currentIndex);
          dot.setAttribute('aria-selected', i === currentIndex ? 'true' : 'false');
        });
      }

      function next() { goTo((currentIndex + 1) % totalCards); }
      function prev() { goTo((currentIndex - 1 + totalCards) % totalCards); }

      // Auto play
      function startAutoPlay() {
        stopAutoPlay();
        autoPlayTimer = setInterval(next, 4000);
      }
      function stopAutoPlay() {
        if (autoPlayTimer) { clearInterval(autoPlayTimer); autoPlayTimer = null; }
      }

      // Button clicks
      nextBtn.addEventListener('click', () => { next(); stopAutoPlay(); startAutoPlay(); });
      prevBtn.addEventListener('click', () => { prev(); stopAutoPlay(); startAutoPlay(); });

      // Dot clicks
      dots.forEach((dot, i) => {
        dot.addEventListener('click', () => { goTo(i); stopAutoPlay(); startAutoPlay(); });
      });

      // Pause on hover
      const carouselOuter = document.querySelector('.carousel-outer');
      carouselOuter.addEventListener('mouseenter', stopAutoPlay);
      carouselOuter.addEventListener('mouseleave', startAutoPlay);

      // Touch / pointer swipe support
      const viewport = document.querySelector('.carousel-viewport');

      viewport.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
        stopAutoPlay();
      }, { passive: true });

      viewport.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
          diff > 0 ? next() : prev();
        }
        startAutoPlay();
      }, { passive: true });

      // Pointer (mouse drag) support
      viewport.addEventListener('pointerdown', (e) => {
        startX = e.clientX;
        isDragging = true;
        stopAutoPlay();
      });
      window.addEventListener('pointerup', (e) => {
        if (!isDragging) return;
        isDragging = false;
        const diff = startX - e.clientX;
        if (Math.abs(diff) > 50) {
          diff > 0 ? next() : prev();
        }
        startAutoPlay();
      });

      // Keyboard navigation
      carouselOuter.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { prev(); e.preventDefault(); }
        if (e.key === 'ArrowRight') { next(); e.preventDefault(); }
      });

      // Recalculate on resize (debounced)
      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => { goTo(currentIndex); }, 150);
      }, { passive: true });

      // Click on card to navigate to it
      cards.forEach((card, i) => {
        card.addEventListener('click', (e) => {
          if (i !== currentIndex) {
            e.preventDefault();
            goTo(i);
            stopAutoPlay();
            startAutoPlay();
          }
        });
      });

      // Initialize
      // Start centered on index 0 (or index 2 for desktop showing 5)
      function init() {
        const visible = getVisibleCount();
        const startIdx = visible >= 5 ? 2 : visible >= 3 ? 1 : 0;
        // Initial transition is instant
        track.style.transition = 'none';
        goTo(startIdx);
        // Re-enable transition on next frame
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            track.style.transition = 'transform 0.5s cubic-bezier(0.4,0,0.2,1)';
          });
        });
        startAutoPlay();
      }

      // Wait for fonts/layout to settle
      if (document.readyState === 'complete') {
        init();
      } else {
        window.addEventListener('load', init);
      }

    })();

    // ---- 5. FAQ ACCORDION ----
    (function() {
      const questions = document.querySelectorAll('.faq-question');
      questions.forEach(btn => {
        btn.addEventListener('click', () => {
          const isOpen = btn.classList.contains('open');
          // Close all
          questions.forEach(q => {
            q.classList.remove('open');
            q.setAttribute('aria-expanded', 'false');
            const ans = q.nextElementSibling;
            if (ans) ans.classList.remove('open');
          });
          // Open clicked (if was closed)
          if (!isOpen) {
            btn.classList.add('open');
            btn.setAttribute('aria-expanded', 'true');
            const ans = btn.nextElementSibling;
            if (ans) ans.classList.add('open');
          }
        });
      });
    })();

    // ---- 6. SCROLL ANIMATIONS (Intersection Observer) ----
    (function() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

      document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));
    })();

    // ---- 7. SMOOTH SCROLL for anchor links ----
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          const offset = 80; // nav height
          const top = target.getBoundingClientRect().top + window.scrollY - offset;
          window.scrollTo({ top, behavior: 'smooth' });
        }
      });
    });

  
</script>

<?php wp_footer(); ?>
</body>
</html>
<?php // End: Fenben® Landing Page Template ?>
