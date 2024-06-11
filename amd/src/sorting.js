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
 * Quicklink items sortable.
 *
 * @package   block_mmquicklink
 * @copyright 2020 Mediamaisteri Oy
 * @author    Mikko Haikonen <mikko.haikonen@mediamaisteri.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Sortable from 'block_mmquicklink/sortable';
import Ajax from 'core/ajax';

const updateSorting = (parent, children) => {
    let promises = Ajax.call([
        { methodname: 'block_mmquicklink_update_sorting', args: {"parent": parent, "children" : children}}
    ]);
    promises[0].done(function(response) {
    }).fail(function(ex) {
        console.log(ex);
    });
}

export const init = () => {

    var nestedSortables = [].slice.call(document.querySelectorAll('.nested-sortable'));
    
    // Loop through each nested sortable element
    for (var i = 0; i < nestedSortables.length; i++) {
        let group = "";
        if (nestedSortables[i].classList.contains('expand')) {
            group = "nested";
        } else {
            group = "mainlist";
        }
        let sortable = new Sortable(nestedSortables[i], {
            group: group,
            sort: true,
            animation: 150,
            filter: ".mmquicklink-mainbtn",
            fallbackOnBody: true,
            swapThreshold: 0.65,
            dataIdAttr: 'data-sortable-id',
            // Element is removed from the list into another list
            onRemove: function (evt) {
                updateSorting(evt.from.id, sortable.toArray());
            },
            // Changed sorting within list
            onUpdate: function (evt) {
                updateSorting(evt.to.id, sortable.toArray());
            },
            // Element is dropped into the list from another list
            onAdd: function (evt) {
                updateSorting(evt.to.id, sortable.toArray());
        
            },
        });
    }
};


//grunt amd --force --root=blocks/mmquicklink