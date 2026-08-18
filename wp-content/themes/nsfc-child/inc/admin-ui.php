<?php
defined( 'ABSPATH' ) || exit;

// Hooked to admin_print_footer_scripts, NOT admin_enqueue_scripts, and that is
// load-bearing. Carbon Fields enqueues carbon-fields-core.css and
// carbon-fields-metaboxes.css from `admin_print_footer_scripts` at priority 9
// (Loader::enqueue_assets), i.e. in the footer. Anything enqueued the normal way
// prints in the head and therefore loses to Carbon Fields at equal specificity —
// which it is, since both style .cf-complex__group-head as a single class.
// Running at priority 10 on the same hook puts this stylesheet immediately after
// theirs, so the overrides win without !important on every rule.
add_action( 'admin_print_footer_scripts', 'nsfc_admin_styles', 10 );
add_filter( 'rest_prepare_program', 'nsfc_hide_inline_term_creation' );
add_filter( 'rest_prepare_camp-session', 'nsfc_hide_inline_term_creation' );

/**
 * The four fixed vocabularies, used by the term-screen tweaks below.
 */
const NSFC_FIXED_TAXONOMIES = [ 'season', 'program_level', 'program_location', 'camp_type' ];

foreach ( NSFC_FIXED_TAXONOMIES as $nsfc_taxonomy ) {
    add_filter( "manage_edit-{$nsfc_taxonomy}_columns", 'nsfc_hide_term_description_column' );
}
unset( $nsfc_taxonomy );

/**
 * Load assets/admin.css on the screens that render Carbon Fields boxes.
 *
 * Post/page editors carry the post_meta containers; the term screens carry the
 * Camp Type Description term_meta container. Nothing else in wp-admin needs it,
 * so it isn't loaded globally.
 *
 * Versioned by the file's own mtime rather than the theme version: this is an
 * admin-only stylesheet that gets tweaked far more often than style.css gets a
 * version bump, and stale-cached admin CSS looks exactly like an edit that
 * didn't apply.
 */
function nsfc_admin_styles() {
    // admin_print_footer_scripts passes no argument, so the screen comes from
    // the global the admin sets up rather than from a parameter.
    global $hook_suffix;

    $screens = [ 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ];

    // Carbon Fields theme-options pages (Settings → Financial Assistance) get a
    // generated hook suffix like
    // settings_page_crb_carbon_fields_container_financial_assistance — matched
    // by prefix so adding another options page doesn't need this list edited.
    $is_options_page = false !== strpos( $hook_suffix, 'crb_carbon_fields_container' );

    if ( ! $is_options_page && ! in_array( $hook_suffix, $screens, true ) ) {
        return;
    }

    $path = get_stylesheet_directory() . '/assets/admin.css';

    wp_enqueue_style(
        'nsfc-admin',
        get_stylesheet_directory_uri() . '/assets/admin.css',
        [],
        file_exists( $path ) ? filemtime( $path ) : wp_get_theme()->get( 'Version' )
    );
}

/**
 * Drop the "Add New Season / Level / Location / Camp Type" form from the
 * taxonomy panels in the Program and Camp Session editors. The checkboxes
 * themselves are untouched — only the inline create form goes away.
 *
 * All four are fixed vocabularies. Terms belong on their own admin screens
 * (Programs → Locations, Camp Sessions → Camp Types), which are the only place
 * that offers a Slug field, and where adding a location is step 1 of 4 in
 * documentation/adding-a-location.md. Created inline from a post instead, you
 * get an auto-slugged term that immediately shows up in every location
 * dropdown and admin filter on the site with no Location Hub page behind it.
 *
 * Core gates that form on the post's `wp:action-create-{taxonomy}` REST link,
 * added by WP_REST_Posts_Controller::get_available_actions() for anyone who
 * can `edit_terms`. Removing the link rather than the capability leaves the
 * term admin screens fully usable.
 */
function nsfc_hide_inline_term_creation( $response ) {
    foreach ( NSFC_FIXED_TAXONOMIES as $taxonomy ) {
        $response->remove_link( 'https://api.w.org/action-create-' . $taxonomy );
    }

    return $response;
}

/**
 * Drop the Description column from the Season / Level / Location / Camp Type
 * term list tables.
 *
 * The Description *field* is hidden in assets/admin.css (nothing in the theme
 * reads a term description), so the column would only ever be empty. Filtering
 * the column out here rather than hiding it in CSS also removes it from Screen
 * Options, where a CSS-hidden column would still be offered as a toggle.
 */
function nsfc_hide_term_description_column( $columns ) {
    unset( $columns['description'] );

    return $columns;
}

add_action( 'edit_form_after_title', 'nsfc_editor_intro_for_post' );

foreach ( [ 'program_location', 'camp_type' ] as $nsfc_intro_taxonomy ) {
    add_action( "{$nsfc_intro_taxonomy}_pre_add_form", 'nsfc_editor_intro_for_taxonomy' );
}
unset( $nsfc_intro_taxonomy );

/**
 * "What goes here" copy for each add/edit screen, keyed by post type or
 * taxonomy. One place to edit the wording rather than a function per screen.
 *
 * Each panel answers the two questions the screen itself can't: which of the
 * similar-looking things this one is for, and which sidebar terms are required.
 * That second point matters more than it looks — a program or camp session
 * missing a term is filtered out of every listing page and simply never
 * appears, with nothing on screen to say why.
 */
function nsfc_editor_intros() {
    // Built here rather than as constants so the URLs go through admin_url().
    $camps      = admin_url( 'edit.php?post_type=camp-session' );
    $camps_new  = admin_url( 'post-new.php?post_type=camp-session' );
    $programs   = admin_url( 'edit.php?post_type=program' );
    $prog_new   = admin_url( 'post-new.php?post_type=program' );
    $venues     = admin_url( 'edit.php?post_type=venue' );
    $venues_new = admin_url( 'post-new.php?post_type=venue' );
    $ages       = admin_url( 'edit.php?post_type=age-group' );
    $ages_new   = admin_url( 'post-new.php?post_type=age-group' );
    $camp_types = admin_url( 'edit-tags.php?taxonomy=camp_type&post_type=camp-session' );
    $locations  = admin_url( 'edit-tags.php?taxonomy=program_location&post_type=program' );
    $pages      = admin_url( 'edit.php?post_type=page' );

    // Carbon Fields derives this slug from the container title ("Financial
    // Assistance"). If that title is ever renamed, this link needs renaming too.
    $financial  = admin_url( 'options-general.php?page=crb_carbon_fields_container_financial_assistance.php' );

    return [
        'program' => [
            'lead'  => sprintf(
                '<strong>Programs are everything that isn&rsquo;t a camp</strong> &mdash; leagues, classes, academies and training. Camps and clinics are added under <a href="%s">Camp Sessions</a> instead.',
                esc_url( $camps )
            ),
            'steps' => [
                'Give it a title, then work down the <strong>Program Details</strong> box. It&rsquo;s in the same order as the finished page.',
                'Fill in both summaries. <strong>Intro</strong> shows on this program&rsquo;s own page; <strong>Card description</strong> shows on the listing pages.',
                sprintf(
                    '<strong>Age range</strong> and <strong>Venue</strong> are both picked from lists. If what you need isn&rsquo;t there, add it under <a href="%s">Age Groups</a> or <a href="%s">Venues</a> first.',
                    esc_url( $ages_new ),
                    esc_url( $venues_new )
                ),
                'Tick <strong>Season</strong>, <strong>Level</strong> and <strong>Location</strong> in the sidebar. A program stays hidden from every listing page until all three are set.',
            ],
            'links' => [
                [ 'Add a Camp Session', $camps_new ],
                [ 'Venues', $venues ],
                [ 'Age Groups', $ages ],
                [ 'Financial assistance wording', $financial ],
            ],
        ],
        'camp-session' => [
            'lead'  => sprintf(
                '<strong>Camp Sessions are single, dated camps and clinics</strong> &mdash; one entry per camp. Anything that runs weekly through a season is a <a href="%s">Program</a> instead.',
                esc_url( $programs )
            ),
            'steps' => [
                'Title it with the camp name as you want it read on the site.',
                sprintf(
                    'Fill in <strong>Camp Session Details</strong> &mdash; dates, cost and the registration link. <strong>Ages</strong> and <strong>Venue</strong> come from the <a href="%s">Age Groups</a> and <a href="%s">Venues</a> lists.',
                    esc_url( $ages ),
                    esc_url( $venues )
                ),
                sprintf(
                    'Tick <strong>Camp Type</strong>, <strong>Season</strong> and <strong>Location</strong> in the sidebar. Without Season and Location it won&rsquo;t appear on any camps page; the <a href="%s">Camp Type</a> supplies the description shown in its pop-up.',
                    esc_url( $camp_types )
                ),
            ],
            'links' => [
                [ 'Add a Program', $prog_new ],
                [ 'Venues', $venues ],
                [ 'Age Groups', $ages ],
                [ 'Camp Types', $camp_types ],
            ],
        ],
        'venue' => [
            'lead'  => '<strong>A venue is a place</strong> &mdash; a field, complex or building. Add it once here and every program and camp that meets there picks it from a list, so the name stays the same everywhere.',
            'steps' => [
                'Title it with the venue&rsquo;s name only &mdash; leave the address out of the name.',
                'Add the address and Google Maps link if you have them. Both can be filled in later; the venue works without either.',
                'Tick the <strong>Location</strong> it belongs to in the sidebar.',
            ],
            'links' => [
                [ 'Programs', $programs ],
                [ 'Camp Sessions', $camps ],
                [ 'Age Groups', $ages ],
            ],
        ],
        'age-group' => [
            'lead'  => '<strong>An age group is one age range</strong> &mdash; every program and camp picks its range from this list, so the wording stays the same across the site.',
            'steps' => [
                'Title it exactly as it should read on the site &mdash; that title <em>is</em> the badge on the listing cards.',
                'Use the <strong>Order</strong> box under Page Attributes to control where it sits in the dropdown. Alphabetical would put U13 before U6.',
                'Three styles are currently in use (<code>U9&ndash;U14</code>, <code>Ages 5&ndash;9</code>, <code>K&ndash;6th grade</code>). Merging them into one house style is safe &mdash; retitle the one you keep, repoint anything using the others, then delete them.',
            ],
            'links' => [
                [ 'Programs', $programs ],
                [ 'Camp Sessions', $camps ],
                [ 'Venues', $venues ],
            ],
        ],
        'program_location' => [
            'lead'  => '<strong>Adding a location here is the first step, not the only one.</strong> The location won&rsquo;t appear on the site until its pages exist.',
            'steps' => [
                'Add the location below with a name and a slug (the short, lower-case version with dashes instead of spaces).',
                'Then create its pages: a Location Hub, plus Competitive, Recreational and Camps beneath it.',
                'The full walkthrough is in <code>documentation/adding-a-location.md</code>.',
            ],
            'links' => [
                [ 'Pages', $pages ],
                [ 'Venues', $venues ],
            ],
        ],
        'camp_type' => [
            'lead'  => '<strong>A camp type is a kind of camp</strong> &mdash; Technical, Goalkeeper, World Cup. Its description is written once here and shown in the pop-up for every camp session of that type.',
            'steps' => [
                'Add the type below with a name and a slug.',
                'Then open it from the list on the right and fill in its <strong>description</strong> &mdash; that&rsquo;s the copy visitors read.',
            ],
            'links' => [
                [ 'Camp Sessions', $camps ],
            ],
        ],
    ];
}

/**
 * Render one panel. Copy is trusted theme markup, so the strong/code tags in
 * nsfc_editor_intros() are allowed through deliberately.
 */
function nsfc_render_editor_intro( $intro ) {
    if ( empty( $intro['lead'] ) ) {
        return;
    }
    ?>
    <div class="nsfc-editor-intro">
        <p class="nsfc-editor-intro__lead"><?php echo wp_kses_post( $intro['lead'] ); ?></p>
        <?php if ( ! empty( $intro['steps'] ) ) : ?>
        <ol class="nsfc-editor-intro__steps">
            <?php foreach ( $intro['steps'] as $step ) : ?>
                <li><?php echo wp_kses_post( $step ); ?></li>
            <?php endforeach; ?>
        </ol>
        <?php endif; ?>
        <?php if ( ! empty( $intro['links'] ) ) : ?>
        <p class="nsfc-editor-intro__links">
            <?php foreach ( $intro['links'] as $link ) : ?>
                <a href="<?php echo esc_url( $link[1] ); ?>"><?php echo esc_html( $link[0] ); ?></a>
            <?php endforeach; ?>
        </p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Post editors — between the title field and the meta boxes.
 *
 * edit_form_after_title works here because none of these CPTs declare `editor`
 * support, so they all use the classic editor screen (see inc/cpt.php). If one
 * ever gains the block editor, its panel will silently stop appearing.
 */
function nsfc_editor_intro_for_post( $post ) {
    $intros = nsfc_editor_intros();

    if ( ! $post || ! isset( $intros[ $post->post_type ] ) ) {
        return;
    }

    nsfc_render_editor_intro( $intros[ $post->post_type ] );
}

/**
 * Term screens — above the "Add New" form on edit-tags.php.
 */
function nsfc_editor_intro_for_taxonomy( $taxonomy ) {
    $intros = nsfc_editor_intros();

    if ( ! isset( $intros[ $taxonomy ] ) ) {
        return;
    }

    nsfc_render_editor_intro( $intros[ $taxonomy ] );
}

/**
 * Taxonomy sidebar box: checkboxes only, no "+ Add New" form and no tabs.
 *
 * Replaces core's post_categories_meta_box() as the `meta_box_cb` for the four
 * fixed vocabularies. Core renders an "Add New {Term}" form there for anyone who
 * can `edit_terms` (wp-admin/includes/meta-boxes.php, line ~676), which is the
 * same inline-creation route already closed off in the block editor by
 * nsfc_hide_inline_term_creation() above.
 *
 * That REST filter is currently inert: Programs and Camp Sessions dropped
 * `editor` support, so they use the classic editor and never load the block
 * editor's term panel. It is kept because it costs nothing and would matter
 * again if either CPT regained `editor`.
 *
 * Terms belong on their own admin screens, which are the only place with a Slug
 * field and where adding a location is step 1 of several — see the panels in
 * nsfc_editor_intros().
 */
function nsfc_taxonomy_checkbox_meta_box( $post, $box ) {
    $taxonomy = $box['args']['taxonomy'] ?? '';
    if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
        return;
    }
    ?>
    <div id="taxonomy-<?php echo esc_attr( $taxonomy ); ?>" class="categorydiv">
        <div id="<?php echo esc_attr( $taxonomy ); ?>-all" class="tabs-panel">
            <?php
            // Without this, unticking every box submits nothing and the existing
            // terms are left in place instead of being cleared.
            ?>
            <input type="hidden" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]" value="0" />
            <ul id="<?php echo esc_attr( $taxonomy ); ?>checklist" class="categorychecklist form-no-clear">
                <?php
                wp_terms_checklist(
                    $post->ID,
                    [
                        'taxonomy'      => $taxonomy,
                        'checked_ontop' => false,
                    ]
                );
                ?>
            </ul>
        </div>
    </div>
    <?php
}
