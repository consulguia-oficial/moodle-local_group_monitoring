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
 * English strings for the Group Monitoring plugin.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Monitoramento de grupos';
$string['pluginnamesettings'] = 'Configurações do monitoramento de grupos';
$string['dashboard'] = 'Painel de monitoramento de grupos';
$string['groups'] = 'Grupos';
$string['creategroup'] = 'Criar grupo';
$string['groupdetail'] = 'Detalhes do grupo';
$string['members'] = 'Membros';
$string['addmember'] = 'Adicionar membro';
$string['importcsv'] = 'Importar via CSV';
$string['courseid'] = 'Curso';
$string['polo'] = 'Polo';
$string['tutor'] = 'Tutor';
$string['groupname'] = 'Nome do grupo';
$string['username'] = 'Nome de usuário';
$string['fullname'] = 'Nome completo';
$string['added_via'] = 'Adicionado via';
$string['added_via_manual'] = 'Manual';
$string['added_via_csv'] = 'CSV';
$string['error_no_polo'] = 'Nenhum polo encontrado para o curso selecionado.';
$string['error_no_tutor'] = 'Nenhum tutor associado a este polo.';
$string['error_duplicate'] = 'Já existe um grupo com este nome neste curso.';
$string['error_csv_empty'] = 'Nenhum nome de usuário foi encontrado no CSV enviado.';
$string['error_csv_invalid'] = 'Arquivo CSV inválido. Esperado um nome de usuário por linha.';
$string['error_invalidemail'] = 'Endereço de e-mail inválido.';
$string['error_invalidrole'] = 'O papel selecionado não é permitido para criação de tutor.';
$string['error_unexpected'] = 'Ocorreu um erro inesperado. Entre em contato com o administrador do site.';
$string['error_usernotfound'] = 'O usuário {$a} não foi encontrado no Moodle.';
$string['error_usernameexists'] = 'Este nome de usuário já existe no Moodle.';
$string['group_created'] = 'Grupo "{$a}" criado com sucesso.';
$string['group_created_plain'] = 'Grupo criado com sucesso.';
$string['group_deleted'] = 'Grupo excluído com sucesso.';
$string['member_added'] = 'Membro adicionado com sucesso.';
$string['members_imported'] = '{$a} membros importados com sucesso.';
$string['polo_field_created'] = 'O campo de perfil configurado para polo está disponível.';
$string['tutor_created'] = 'Tutor criado e matriculado com sucesso.';
$string['courses'] = 'Cursos';
$string['polos'] = 'Polos';
$string['tutors'] = 'Tutores';
$string['polofield'] = 'Campo de perfil para polo';
$string['polofield_desc'] = 'Selecione o campo de perfil de usuário personalizado que será usado para agrupar os polos. Caso nenhum campo exista, o campo padrão "polo" será usado e criado.';

// ---------------------------------------------------------------------------
// Capability strings - chaves corretas no formato Frankenstyle
// ---------------------------------------------------------------------------
$string['group_monitoring:view'] = 'Ver painel de monitoramento de grupos';
$string['group_monitoring:creategroup'] = 'Criar grupos monitorados';
$string['group_monitoring:addmember'] = 'Adicionar membros a grupos monitorados';
$string['group_monitoring:createtutor'] = 'Criar usuários tutores';
$string['group_monitoring:managepolo'] = 'Gerenciar campo de perfil de polo';
$string['group_monitoring:deletegroup'] = 'Excluir grupos monitorados';
$string['group_monitoring:config'] = 'Configurar plugin de monitoramento de grupos';

// ---------------------------------------------------------------------------
// Strings de interface (Edição 5 - internacionalização)
// ---------------------------------------------------------------------------
$string['subtitle'] = 'Visão geral e gestão do Monitoramento de Grupos';
$string['tab_dashboard'] = 'Dashboard';
$string['current_course'] = 'Curso Atual';
$string['students_linked'] = 'alunos vinculados';
$string['no_members'] = 'Nenhum aluno vinculado.';
$string['no_groups'] = 'Nenhum grupo monitorado neste curso. Crie um novo grupo acima.';
$string['no_polos'] = 'Nenhum polo registrado nos usuários. Clique no botão acima para garantir a estrutura e cadastre tutores em seguida.';
$string['no_tutors'] = 'Nenhum usuário com o papel de tutor matriculado neste curso.';
$string['students'] = 'Cursistas';
$string['btn_polo'] = 'Garantir Campo Polo';
$string['btn_tutor'] = 'Novo Tutor';

// Títulos e botões dos modais
$string['modal_title_polo'] = 'Garantir Campo Polo';
$string['modal_btn_polo'] = 'Criar Campo';
$string['modal_hint_polo'] = 'Isto criará o campo profile_field_polo nativamente no Moodle.';
$string['modal_title_tutor'] = 'Novo Tutor';
$string['modal_btn_tutor'] = 'Criar Tutor';
$string['modal_title_group'] = 'Novo Grupo';
$string['modal_btn_group'] = 'Criar Grupo';
$string['modal_add_to'] = 'Adicionar a {$a}';
$string['modal_btn_add_member'] = 'Adicionar Aluno';
$string['modal_import_to'] = 'Importar CSV para {$a}';
$string['modal_btn_import'] = 'Importar Alunos';

// Rótulos de campos de formulário nos modais AMD
$string['form_firstname'] = 'Primeiro nome *';
$string['form_lastname'] = 'Sobrenome *';
$string['form_email'] = 'Email *';
$string['form_username_req'] = 'Username *';
$string['form_password'] = 'Senha Inicial *';
$string['form_role'] = 'Papel (Role) no Curso *';
$string['form_polo_optional'] = 'Vincular a um Polo';
$string['form_polo_placeholder'] = 'Digite o nome do polo (Opcional)';
$string['form_polo_existing'] = 'Polo Existente *';
$string['form_tutor_select'] = 'Tutor (Usuário) *';
$string['form_group_hint'] = 'Formato final gerado: Polo_Nome_InicialSobrenome';
$string['form_select_placeholder'] = 'Selecione...';
$string['form_student_username'] = 'Username do Aluno *';
$string['form_csv_file'] = 'Ficheiro CSV *';
$string['form_csv_hint'] = 'O ficheiro deve conter os usernames dos alunos, sendo um por linha.';

// ---------------------------------------------------------------------------
// Strings de privacidade - nomes de tabela no padrão Frankenstyle (Edição 2)
// ---------------------------------------------------------------------------
$string['privacy:metadata:local_group_monitoring_groups'] = 'Armazena grupos monitorados criados e gerenciados pelo plugin de Monitoramento de Grupos.';
$string['privacy:metadata:local_group_monitoring_groups:name'] = 'O nome do grupo monitorado.';
$string['privacy:metadata:local_group_monitoring_groups:courseid'] = 'O curso onde o grupo monitorado foi criado.';
$string['privacy:metadata:local_group_monitoring_groups:polo'] = 'O polo associado ao grupo monitorado.';
$string['privacy:metadata:local_group_monitoring_groups:tutorid'] = 'O ID do usuário tutor associado ao grupo monitorado.';
$string['privacy:metadata:local_group_monitoring_groups:moodle_groupid'] = 'O ID do grupo Moodle correspondente.';
$string['privacy:metadata:local_group_monitoring_groups:timecreated'] = 'O momento em que o grupo monitorado foi criado.';
$string['privacy:metadata:local_group_monitoring_groups:timemodified'] = 'O momento da última modificação do grupo monitorado.';
$string['privacy:metadata:local_group_monitoring_groups:usermodified'] = 'O ID do usuário que modificou o grupo monitorado por último.';
$string['privacy:metadata:local_group_monitoring_members'] = 'Armazena membros adicionados aos grupos monitorados.';
$string['privacy:metadata:local_group_monitoring_members:groupid'] = 'O ID do grupo monitorado.';
$string['privacy:metadata:local_group_monitoring_members:userid'] = 'O ID do usuário membro do grupo.';
$string['privacy:metadata:local_group_monitoring_members:username'] = 'O nome de usuário do membro do grupo.';
$string['privacy:metadata:local_group_monitoring_members:added_via'] = 'O método usado para adicionar o membro ao grupo.';
$string['privacy:metadata:local_group_monitoring_members:timecreated'] = 'O momento em que o membro foi adicionado.';
$string['privacy:metadata:local_group_monitoring_members:usermodified'] = 'O ID do usuário que adicionou ou modificou o registro do membro por último.';
$string['privacy:metadata:local_group_monitoring_polos'] = 'Armazena polos gerenciados pelo plugin de Monitoramento de Grupos.';
$string['privacy:metadata:local_group_monitoring_polos:name'] = 'O nome do polo.';
$string['privacy:metadata:local_group_monitoring_polos:timecreated'] = 'O momento em que o polo foi criado.';
$string['privacy:metadata:local_group_monitoring_polos:timemodified'] = 'O momento da última modificação do polo.';
$string['privacy:metadata:local_group_monitoring_polos:usermodified'] = 'O ID do usuário que modificou o polo por último.';
