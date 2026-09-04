<?php

class Create_users_table {

    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('users')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE,
                ],
                'firstname' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'lastname' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => FALSE,
                    'unique'     => TRUE,
                ],
                'username' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                    'unique'     => TRUE,
                ],
            ])
            ->add_key('id', primary: TRUE)
            ->add_key('username', unique: TRUE, name: 'username_unique')
            ->add_key('email', unique: TRUE, name: 'email_unique')
            ->create_table('users');

        $this->_lava->db->table('users')->bulk_insert([
            ['firstname' => 'Juan',  'lastname' => 'Dela Cruz', 'email' => 'juan@example.com',  'username' => 'juandelacruz'],
            ['firstname' => 'Maria', 'lastname' => 'Santos',    'email' => 'maria@example.com', 'username' => 'mariasantos'],
            ['firstname' => 'Pedro', 'lastname' => 'Garcia',    'email' => 'pedro@example.com', 'username' => 'pedrogarcia'],
            ['firstname' => 'Ana',   'lastname' => 'Reyes',     'email' => 'ana@example.com',   'username' => 'anareyes'],
            ['firstname' => 'Jose',  'lastname' => 'Mendoza',   'email' => 'jose@example.com',  'username' => 'josemendoza'],
        ]);
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('users');
    }
}
