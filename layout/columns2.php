<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Boost Union - Columns2 page layout.
 *
 * This layoutfile is based on theme/boost/layout/columns2.php
 *
 * Modifications compared to this layout file:
 * * Include activity navigation
 * * Include course related hints
 * * Include back to top button
 * * Include scroll spy
 * * Include footnote
 * * Include static pages
 * * Include Jvascript disabled hint
 * * Include info banners
 * * Include smart menus
 *
 * @package   theme_learnr
 * @copyright 2022 Luca Bösch, BFH Bern University of Applied Sciences luca.boesch@bfh.ch
 * @copyright based on code from theme_boost by Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');

// Require own locallib.php.
require_once($CFG->dirroot . '/theme/learnr/locallib.php');

// Add activity navigation if the feature is enabled.
$activitynavigation = get_config('theme_learnr', 'activitynavigation');
if ($activitynavigation == THEME_LEARNR_SETTING_SELECT_YES) {
    $PAGE->theme->usescourseindex = false;
}

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

$extraclasses = [];
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

// Load the navigation from learnr primary navigation, the extended version of core primary navigation.
// It includes the smart menus and menu items, for multiple locations.
$primary = new theme_learnr\output\navigation\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);

// Add a special class selector to improve the Smart menus SCSS selectors.
if (isset($primarymenu['includesmartmenu']) && $primarymenu['includesmartmenu'] == true) {
    $extraclasses[] = 'theme-boost-union-smartmenu';
}

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions()  && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$bodyattributes = $OUTPUT->body_attributes($extraclasses); // In the original layout file, this line is place more above,
                                                           // but we amended $extraclasses and had to move it.

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'headercontent' => $headercontent,
    'overflow' => $overflow,
    'addblockbutton' => $addblockbutton,
];

// Include the template content for the course related hints.
require_once(__DIR__ . '/includes/courserelatedhints.php');

// Include the content for the back to top button.
require_once(__DIR__ . '/includes/backtotopbutton.php');

// Include the content for the scrollspy.
require_once(__DIR__ . '/includes/scrollspy.php');

// Include the template content for the footnote.
require_once(__DIR__ . '/includes/footnote.php');

// Include the template content for the static pages.
require_once(__DIR__ . '/includes/staticpages.php');

// Include the template content for the JavaScript disabled hint.
require_once(__DIR__ . '/includes/javascriptdisabledhint.php');

// Include the template content for the info banners.
require_once(__DIR__ . '/includes/infobanners.php');

// Include the template content for the navbar styling.
require_once(__DIR__ . '/includes/navbar.php');

// Include the template content for the smart menus.
require_once(__DIR__ . '/includes/smartmenus.php');

// Render columns2.mustache from theme_boost (which is overridden in theme_learnr).
echo $OUTPUT->render_from_template('theme_boost/columns2', $templatecontext);
