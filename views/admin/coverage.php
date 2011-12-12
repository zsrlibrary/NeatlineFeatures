<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>
<div id="<? echo $id_prefix ?>widget" class='nlfeatures nlfeatures-edit'>
  <div class='nlfeatures-edit-nav'>
    <div class='nlfeatures-edit-nav-menu'>
      <ul>
        <li class='selected'><a href="#">NL Features</a></li><li><a href="#">Raw</a></li>
      </ul>
    </div>
    <div class='nlfeatures-edit-nav-tools'>
    </div>
  </div>
  <div class='nlfeatures-edit-body'>
    <div class='nlfeatures-edit-raw'>
      <em>This space intentionally left blank.</em>
    </div>
    <div class='nlfeatures-edit-map'>
      <div id="<? echo $id_prefix ?>map"></div>
    </div>
  </div>
</div>
<script type='text/javascript'>
(function($) {
    var el = $('#<? echo $id_prefix ?>map');
    var m = el.nlfeatures({
        map: {
            // Sri Lanka, just cause it's fun to say.
            // center: [8986896.64319, 866942.16213],
            center: [-8738850.21367, 4584105.47978],
            zoom: 3
            }
        })
        .data('nlfeatures');
    var item = {
        id: el.attr('id'),
        title: "Coverage",
        name: 'Coverage',
        wkt: "POINT (-8738850.21367 4584105.47978)"
        };
    m.loadLocalData([item]);
    m.editJson(item, true);
})(jQuery);
</script>
<style type='text/css'>
<? echo '#' . $id_prefix ?>map {
    border: 2px solid red;
    width: 100%;
    height: 300px;
}
</style>