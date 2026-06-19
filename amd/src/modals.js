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
 * AMD module for Group Monitoring modal dialogs.
 *
 * All user-visible strings are loaded via core/str to support full i18n.
 *
 * @module     local_group_monitoring/modals
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory', 'core/modal_events', 'core/str'],
function($, ModalFactory, ModalEvents, Str) {

    /**
     * All string keys loaded once via core/str at initialisation time.
     * Dynamic strings (containing {$a}) are fetched on demand.
     */
    var STRINGS = [
        {key: 'modal_title_polo',      component: 'local_group_monitoring'}, // 0
        {key: 'modal_btn_polo',        component: 'local_group_monitoring'}, // 1
        {key: 'modal_hint_polo',       component: 'local_group_monitoring'}, // 2
        {key: 'modal_title_tutor',     component: 'local_group_monitoring'}, // 3
        {key: 'modal_btn_tutor',       component: 'local_group_monitoring'}, // 4
        {key: 'form_firstname',        component: 'local_group_monitoring'}, // 5
        {key: 'form_lastname',         component: 'local_group_monitoring'}, // 6
        {key: 'form_email',            component: 'local_group_monitoring'}, // 7
        {key: 'form_username_req',     component: 'local_group_monitoring'}, // 8
        {key: 'form_password',         component: 'local_group_monitoring'}, // 9
        {key: 'form_role',             component: 'local_group_monitoring'}, // 10
        {key: 'form_polo_optional',    component: 'local_group_monitoring'}, // 11
        {key: 'form_polo_placeholder', component: 'local_group_monitoring'}, // 12
        {key: 'modal_title_group',     component: 'local_group_monitoring'}, // 13
        {key: 'modal_btn_group',       component: 'local_group_monitoring'}, // 14
        {key: 'form_polo_existing',    component: 'local_group_monitoring'}, // 15
        {key: 'form_tutor_select',     component: 'local_group_monitoring'}, // 16
        {key: 'form_group_hint',       component: 'local_group_monitoring'}, // 17
        {key: 'form_select_placeholder', component: 'local_group_monitoring'}, // 18
        {key: 'modal_btn_add_member',  component: 'local_group_monitoring'}, // 19
        {key: 'form_student_username', component: 'local_group_monitoring'}, // 20
        {key: 'modal_btn_import',      component: 'local_group_monitoring'}, // 21
        {key: 'form_csv_file',         component: 'local_group_monitoring'}, // 22
        {key: 'form_csv_hint',         component: 'local_group_monitoring'}, // 23
    ];

    /**
     * Build a SAVE_CANCEL modal and bind the submit handler.
     *
     * @param {object} config  title, body, large, saveText
     * @returns {Promise}
     */
    var buildModal = function(config) {
        return ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: config.title,
            body: config.body,
            large: config.large || false
        }).then(function(modal) {
            modal.setSaveButtonText(config.saveText);
            modal.getRoot().on(ModalEvents.save, function(e) {
                e.preventDefault();
                var form = modal.getRoot().find('#groupmon-form')[0];
                if (form && form.checkValidity()) {
                    form.submit();
                } else if (form) {
                    form.reportValidity();
                }
            });
            modal.show();
            return modal;
        });
    };

    return {
        /**
         * Initialise all modal event listeners.
         * Called from the Mustache template via {{#js}}.
         */
        init: function() {
            Str.get_strings(STRINGS).then(function(s) {

                // ---- Botões deentidades (polo / tutor / grupo) ----
                $(document).on('click', '[id^=btn-new-]', function(e) {
                    e.preventDefault();
                    var btnId    = $(this).attr('id');
                    var container = $('#groupmon-container');
                    var courseid = container.data('courseid');
                    var sesskey  = container.data('sesskey');
                    var rolesOptions  = $('#hidden-roles').html();
                    var polosOptions  = $('#hidden-polos').html();
                    var tutorsOptions = $('#hidden-tutors').html();
                    var title = '', body = '', btnText = '';

                    if (btnId === 'btn-new-polo') {
                        title   = s[0];
                        btnText = s[1];
                        body    = '<form id="groupmon-form"'
                                + ' action="index.php?courseid=' + courseid + '"'
                                + ' method="POST">'
                                + '<input type="hidden" name="sesskey" value="' + sesskey + '">'
                                + '<input type="hidden" name="action" value="createpolo">'
                                + '<p>' + s[2] + '</p>'
                                + '</form>';

                    } else if (btnId === 'btn-new-tutor') {
                        title   = s[3];
                        btnText = s[4];
                        body    = '<form id="groupmon-form"'
                                + ' action="index.php?courseid=' + courseid + '"'
                                + ' method="POST">'
                                + '<input type="hidden" name="sesskey" value="' + sesskey + '">'
                                + '<input type="hidden" name="action" value="createtutor">'
                                + '<div class="row">'
                                +   '<div class="col-md-6"><div class="form-group">'
                                +     '<label class="font-weight-bold">' + s[5] + '</label>'
                                +     '<input type="text" name="firstname" class="form-control" required>'
                                +   '</div></div>'
                                +   '<div class="col-md-6"><div class="form-group">'
                                +     '<label class="font-weight-bold">' + s[6] + '</label>'
                                +     '<input type="text" name="lastname" class="form-control" required>'
                                +   '</div></div>'
                                + '</div>'
                                + '<div class="form-group">'
                                +   '<label class="font-weight-bold">' + s[7] + '</label>'
                                +   '<input type="email" name="email" class="form-control" required>'
                                + '</div>'
                                + '<div class="row">'
                                +   '<div class="col-md-6"><div class="form-group">'
                                +     '<label class="font-weight-bold">' + s[8] + '</label>'
                                +     '<input type="text" name="username" class="form-control" required>'
                                +   '</div></div>'
                                +   '<div class="col-md-6"><div class="form-group">'
                                +     '<label class="font-weight-bold">' + s[9] + '</label>'
                                +     '<input type="password" name="password" class="form-control" required>'
                                +   '</div></div>'
                                + '</div>'
                                + '<div class="form-group">'
                                +   '<label class="font-weight-bold">' + s[10] + '</label>'
                                +   '<select name="roleid" class="form-control" required>'
                                +     '<option value="">' + s[18] + '</option>'
                                +     rolesOptions
                                +   '</select>'
                                + '</div>'
                                + '<div class="form-group">'
                                +   '<label class="font-weight-bold">' + s[11] + '</label>'
                                +   '<input type="text" name="polo" class="form-control"'
                                +     ' list="polo-list" placeholder="' + s[12] + '">'
                                + '</div>'
                                + '</form>';

                    } else if (btnId === 'btn-new-group') {
                        title   = s[13];
                        btnText = s[14];
                        body    = '<form id="groupmon-form"'
                                + ' action="index.php?courseid=' + courseid + '"'
                                + ' method="POST">'
                                + '<input type="hidden" name="sesskey" value="' + sesskey + '">'
                                + '<input type="hidden" name="action" value="creategroup">'
                                + '<div class="form-group">'
                                +   '<label class="font-weight-bold">' + s[15] + '</label>'
                                +   '<select name="polo" class="form-control" required>'
                                +     '<option value="">' + s[18] + '</option>'
                                +     polosOptions
                                +   '</select>'
                                + '</div>'
                                + '<div class="form-group">'
                                +   '<label class="font-weight-bold">' + s[16] + '</label>'
                                +   '<select name="tutorid" class="form-control" required>'
                                +     '<option value="">' + s[18] + '</option>'
                                +     tutorsOptions
                                +   '</select>'
                                + '</div>'
                                + '<p class="text-muted small mt-2">' + s[17] + '</p>'
                                + '</form>';
                    }

                    buildModal({title: title, body: body, saveText: btnText, large: true});
                });

                // ---- Adicionar membro manualmente ----
                $(document).on('click', '.btn-add-manual', function(e) {
                    e.preventDefault();
                    var groupid   = $(this).data('groupid');
                    var groupname = $(this).data('groupname');
                    var courseid  = $('#groupmon-container').data('courseid');
                    var sesskey   = $('#groupmon-container').data('sesskey');

                    Str.get_string('modal_add_to', 'local_group_monitoring', groupname).then(function(title) {
                        var body = '<form id="groupmon-form"'
                                 + ' action="index.php?courseid=' + courseid + '"'
                                 + ' method="POST">'
                                 + '<input type="hidden" name="sesskey" value="' + sesskey + '">'
                                 + '<input type="hidden" name="action" value="addmember">'
                                 + '<input type="hidden" name="groupid" value="' + groupid + '">'
                                 + '<div class="form-group">'
                                 +   '<label class="font-weight-bold">' + s[20] + '</label>'
                                 +   '<input type="text" name="username" class="form-control" required>'
                                 + '</div>'
                                 + '</form>';
                        buildModal({title: title, body: body, saveText: s[19]});
                    });
                });

                // ---- Importar membros via CSV ----
                $(document).on('click', '.btn-add-csv', function(e) {
                    e.preventDefault();
                    var groupid   = $(this).data('groupid');
                    var groupname = $(this).data('groupname');
                    var courseid  = $('#groupmon-container').data('courseid');
                    var sesskey   = $('#groupmon-container').data('sesskey');

                    Str.get_string('modal_import_to', 'local_group_monitoring', groupname).then(function(title) {
                        var body = '<form id="groupmon-form"'
                                 + ' action="index.php?courseid=' + courseid + '"'
                                 + ' method="POST" enctype="multipart/form-data">'
                                 + '<input type="hidden" name="sesskey" value="' + sesskey + '">'
                                 + '<input type="hidden" name="action" value="importcsv">'
                                 + '<input type="hidden" name="groupid" value="' + groupid + '">'
                                 + '<div class="form-group">'
                                 +   '<label class="font-weight-bold">' + s[22] + '</label>'
                                 +   '<input type="file" name="csvfile" class="form-control-file"'
                                 +     ' accept=".csv" required>'
                                 + '</div>'
                                 + '<p class="text-muted small mt-2">' + s[23] + '</p>'
                                 + '</form>';
                        buildModal({title: title, body: body, saveText: s[21]});
                    });
                });

            }).catch(function(err) {
                window.console.error('local_group_monitoring/modals: failed to load strings', err);
            });
        }
    };
});
