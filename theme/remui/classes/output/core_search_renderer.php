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
 * Search renderer.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Search renderer.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_search_renderer extends \core_search\output\renderer {
    /**
     * Renders search results.
     *
     * @param \core_search\document[] $results
     * @param int $page Zero based page number.
     * @param int $totalcount Total number of results available.
     * @param \moodle_url $url
     * @param \core_search\area_category|null $cat Selected search are category or null if category functionality is disabled.
     * @return string HTML
     */
    public function render_results($results, $page, $totalcount, $url, $cat = null) {
        $content = '';

        if (\core_search\manager::is_search_area_categories_enabled() && !empty($cat)) {
            $toprow = [];
            foreach (\core_search\manager::get_search_area_categories() as $category) {
                $taburl = clone $url;
                $taburl->param('cat', $category->get_name());
                $taburl->param('page', 0);
                $taburl->remove_params(['page', 'areaids']);
                $toprow[$category->get_name()] = new \tabobject($category->get_name(), $taburl, $category->get_visiblename());
            }

            if (\core_search\manager::should_hide_all_results_category()) {
                unset($toprow[\core_search\manager::SEARCH_AREA_CATEGORY_ALL]);
            }

            $content .= $this->tabtree($toprow, $cat->get_name());
        }

        // Paging bar.
        $perpage = \core_search\manager::DISPLAY_RESULTS_PER_PAGE;
        $content .= $this->output->paging_bar($totalcount, $page, $perpage, $url);

        // Results.
        $resultshtml = array();
        foreach ($results as $hit) {
            $resultshtml[] = $this->render_result($hit);
        }
        if (empty($resultshtml)) {
            $resultshtml[] = '</hr>'.get_string('noresultfoundmg', 'theme_remui');
        }
        $content .= \html_writer::tag('div', implode('<hr/>', $resultshtml), array('class' => 'search-results'));
        $totalcounthtml = "<div class = 'search-result-count p-mb-4 d-none small-info-semibold'>".get_string('searchtotalcount', 'theme_remui', count($results))."</div>";
        $content .= $totalcounthtml;

        // Paging bar.
        $content .= $this->output->paging_bar($totalcount, $page, $perpage, $url);

        return $content;
    }
}
