<?php

declare(strict_types=1);

/*
 *  $Id: Mysql.php 7644 2010-06-08 15:12:02Z jwage $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * @package     Doctrine
 * @subpackage  Import
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Lukas Smith <smith@pooteeweet.org> (PEAR MDB2 library)
 * @version     $Revision: 7644 $
 * @link        www.doctrine-project.org
 * @since       1.0
 */
class Doctrine_Import_Mysql extends Doctrine_Import
{
    public function listTableColumns(string $table): array
    {
        $sql = 'DESCRIBE ' . $this->conn->quoteIdentifier($table, true);
        $result = $this->conn->fetchAssoc($sql);

        $description = [];
        $columns = [];
        foreach ($result as $key => $val) {

            $val = array_change_key_case($val, CASE_LOWER);

            $decl = $this->conn->dataDict->getPortableDeclaration($val);

            $values = isset($decl['values']) ? $decl['values'] : [];
            $val['default'] = $val['default'] == 'CURRENT_TIMESTAMP' ? null : $val['default'];

            $description = [
                'name'          => $val['field'],
                'type'          => $decl['type'][0],
                'alltypes'      => $decl['type'],
                'ntype'         => $val['type'],
                'length'        => $decl['length'],
                'fixed'         => (bool) $decl['fixed'],
                'unsigned'      => (bool) $decl['unsigned'],
                'values'        => $values,
                'primary'       => (strtolower($val['key']) == 'pri'),
                'default'       => $val['default'],
                'notnull'       => (bool) ($val['null'] != 'YES'),
                'autoincrement' => (bool) (strpos($val['extra'], 'auto_increment') !== false),
            ];
            if (isset($decl['scale'])) {
                $description['scale'] = $decl['scale'];
            }
            $columns[$val['field']] = $description;
        }

        return $columns;
    }
}
