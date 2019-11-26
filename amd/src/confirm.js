define(['jquery', 'core/config','core/templates', 'core/modal_factory', 'core/modal_events','core/str'], function($, mdlcfg, templates, ModalFactory, ModalEvents, str) {

    return {
        init: function(courseid, hide, coursename) {
            // Define data array depending on hide variable.
            if (hide == 1) {
                var data = { hide: hide , 'coursename': coursename};
            } else {
                var data = { 'coursename': coursename };
            }

            // What happens when user clicks hide/show course button.
            $(".btn-hidecourse, .btn-showcourse").click(function(e) {
                // Prevent default link action.
                e.preventDefault();
                createshowhidemodal();
                function createshowhidemodal() {
                    ModalFactory.create({
                        type: ModalFactory.types.SAVE_CANCEL,
                        title: templates.render("block_mmquicklink/modal_hidecourse_title", data),
                        body: templates.render("block_mmquicklink/modal_hidecourse_body", data),
                    }).then(function(modal) {
                        var root = modal.getRoot();
                        root.on(ModalEvents.save, function() {
                            $.get(mdlcfg.wwwroot + '/blocks/mmquicklink/changevisibility.php', { 'id': courseid, 'hide': hide, 'confirm': 1 } ).done(function(data) {
                                // Nothing to do here.
                            });
                            location.reload();
                        });
                        modal.show();
                    });
                }
            });

        }
    }
});
