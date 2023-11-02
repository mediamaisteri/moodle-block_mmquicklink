/* eslint-disable promise/always-return */
/* eslint-disable promise/catch-or-return */
define([
  'jquery',
  'core/config',
  'core/templates',
  'core/modal_factory',
  'core/modal_events',
  'core/str',
  'core/notification',
], function (
  $,
  mdlcfg,
  templates,
  ModalFactory,
  ModalEvents,
  str,
  notification
) {
  return {
    init: function (courseid, hide, coursename, categoryid) {
      // Define variables.
      var showhideaction;
      var data;

      // Define data array depending on hide variable.
      if (hide == 1) {
        showhideaction = 'hidecourse';
        data = { hide: hide, coursename: coursename };
      } else {
        showhideaction = 'showcourse';
        data = { coursename: coursename };
      }

      // Bind click event to show delete course modal to delete course button.
      $('.btn-deletecourse').click(function (e) {
        // Prevent default link action.
        e.preventDefault();
        var templateData = { coursename: coursename };
        render_delete_modal(templateData);
      });

      /**
       * This function creates a delete course modal
       *
       * @param {object} templateData Data used when render the modal
       *
       * @returns void
       */
      function render_delete_modal(templateData) {
        // Defined string want to get
        var strings = [
          {
            key: 'delete_course_modal_title',
            component: 'block_mmquicklink',
          },
          {
            key: 'delete_course_success_msg',
            component: 'block_mmquicklink',
          },
          {
            key: 'delete_course_failed_msg',
            component: 'block_mmquicklink',
          },
        ];

        str.get_strings(strings).then(function (translatedString) {
          ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: templates.render(
              'block_mmquicklink/modal_deletecourse_title',
              templateData
            ),
            body: templates.render(
              'block_mmquicklink/modal_deletecourse_body',
              templateData
            ),
          }).then(function (modal) {
            var deleteButtonText = translatedString[0];
            modal.setSaveButtonText(deleteButtonText);
            var root = modal.getRoot();
            root.on(ModalEvents.save, function () {
              var deleteSuccessMessage = translatedString[1];
              var deleteFailMessage = translatedString[2];

              $.get(mdlcfg.wwwroot + '/blocks/mmquicklink/delete.php', {
                id: courseid,
              })
                .then(function () {
                  notification.addNotification({
                    message: deleteSuccessMessage,
                    type: 'success',
                  });
                  setTimeout(function () {
                    location.href = mdlcfg.wwwroot;
                  }, 1000);
                })
                .catch(function () {
                  notification.addNotification({
                    message: deleteFailMessage,
                    type: 'error',
                  });
                });
            });
            modal.show();
          });
        });
      }

      // What happens when user clicks hide/show course button.
      $('.btn-hidecourse, .btn-showcourse').click(function (e) {
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
        window.console.log(showhideaction); //showcourse hidecourse
        $.get(
          mdlcfg.wwwroot + '/blocks/mmquicklink/checkcompletionsettings.php',
          {
            courseid: courseid,
            action: showhideaction,
            confirm: true,
            sesskey: mdlcfg.sesskey,
          }
        ).done(function (result) {
          data.completionok = result;
          data.url = mdlcfg.wwwroot + `/course/completion.php/?id=${courseid}`;
          ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: templates.render(
              'block_mmquicklink/modal_hidecourse_title',
              data
            ),
            body: templates.render(
              'block_mmquicklink/modal_hidecourse_body',
              data
            ),
          }).then(function (modal) {
            var root = modal.getRoot();
            root.on(ModalEvents.save, function () {
              $.get(mdlcfg.wwwroot + '/course/ajax/management.php', {
                courseid: courseid,
                action: showhideaction,
                confirm: 1,
                sesskey: mdlcfg.sesskey,
              }).done(function () {
                $.get(
                  mdlcfg.wwwroot + '/blocks/mmquicklink/changevisibility.php',
                  {
                    courseid: courseid,
                    action: showhideaction,
                    confirm: 1,
                    sesskey: mdlcfg.sesskey,
                  }
                ).done(function () {
                  // Nothing to do here.
                  location.reload();
                });
              });
            });
            modal.show();
          });
        });
      }

      /**
       * This function creates a archive modal.
       */
      function archive() {
        ModalFactory.create({
          type: ModalFactory.types.SAVE_CANCEL,
          title: templates.render(
            'block_mmquicklink/modal_archivecourse_title',
            data
          ),
          body: templates.render(
            'block_mmquicklink/modal_archivecourse_body',
            data
          ),
        }).then(function (modal) {
          var root = modal.getRoot();
          root.on(ModalEvents.save, function () {
            $.get(mdlcfg.wwwroot + '/blocks/mmquicklink/archive.php', {
              courseid: courseid,
              categoryid: categoryid,
              confirm: 1,
            }).done(function () {
              // Nothing to do here.
              location.reload();
            });
          });
          modal.show();
        });
      }

      // What happens when user clicks archive course button.
      $('.btn-archivecourse').click(function (e) {
        // Prevent default link action.
        e.preventDefault();
        archive();
      });
    },
  };
});
