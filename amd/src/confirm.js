/* eslint-disable promise/always-return */
/* eslint-disable promise/catch-or-return */
define(['jquery', 'core/config','core/templates', 'core/modal_factory', 'core/modal_events','core/str'], function($, mdlcfg, templates, ModalFactory, ModalEvents, str) {

    return {
        init: function(courseid, hide, coursename, categoryid) {

            // Define variables.
            var showhideaction;
            var data;

            // Define data array depending on hide variable.
            if (hide == 1) {
                showhideaction = "hidecourse";
                data = { hide: hide , 'coursename': coursename};
            } else {
                showhideaction = "showcourse";
                data = { 'coursename': coursename };
            }

            // What happens when user clicks hide/show course button.
            $(".btn-hidecourse, .btn-showcourse").click(function(e) {
                // Prevent default link action.
                e.preventDefault();
                createshowhidemodal(showhideaction, data);
            });

            /**
             * This function creates a show/hide modal.
             *
             * @param {string} showhideaction
             * @param {string} data
             */
            function createshowhidemodal(showhideaction, data) {
                ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: templates.render("block_mmquicklink/modal_hidecourse_title", data),
                    body: templates.render("block_mmquicklink/modal_hidecourse_body", data),
                }).then(function(modal) {
                    var root = modal.getRoot();
                    root.on(ModalEvents.save, function() {
                        $.get(mdlcfg.wwwroot + '/course/ajax/management.php', {
                            'courseid': courseid,
                            'action': showhideaction,
                            'confirm': 1,
                            'sesskey': mdlcfg.sesskey,
                        }).done(function() {
                            $.get(mdlcfg.wwwroot + '/blocks/mmquicklink/changevisibility.php', {
                                'courseid': courseid,
                                'action': showhideaction,
                                'confirm': 1,
                                'sesskey': mdlcfg.sesskey,
                            }).done(function() {
                                // Nothing to do here.
                                location.reload();
                            });
                        });

                    });
                    modal.show();
                });
            }

            /**
             * This function creates a archive modal.
             */
            function archive() {
                ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: templates.render("block_mmquicklink/modal_archivecourse_title", data),
                    body: templates.render("block_mmquicklink/modal_archivecourse_body", data),
                }).then(function(modal) {
                    var root = modal.getRoot();
                    root.on(ModalEvents.save, function() {
                        $.get(mdlcfg.wwwroot + '/blocks/mmquicklink/archive.php', { 'courseid': courseid, 'categoryid': categoryid, 'confirm': 1 } ).done(function(data) {
                            // Nothing to do here.
                            location.reload();
                        });

                    });
                    modal.show();
                });
            }

            // What happens when user clicks archive course button.
            $(".btn-archivecourse").click(function(e) {
                // Prevent default link action.
                e.preventDefault();
                archive();
            });

        }
    }
});
