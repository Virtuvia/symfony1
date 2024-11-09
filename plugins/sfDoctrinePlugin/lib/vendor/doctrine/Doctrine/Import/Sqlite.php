<?php

declare(strict_types=1);

/*
 *  $Id: Sqlite.php 7644 2010-06-08 15:12:02Z jwage $
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
class Doctrine_Import_Sqlite extends Doctrine_Import
{
    public function listTableColumns(string $table): array
    {
        $sql    = 'PRAGMA table_info(' . $table . ')';
        $result = $this->conn->fetchAll($sql);

        $description = [];
        $columns     = [];
        foreach ($result as $key => $val) {
            $val = array_change_key_case($val, CASE_LOWER);
            $decl = $this->conn->dataDict->getPortableDeclaration($val);

            $description = [
                'name'          => $val['name'],
                'ntype'         => $val['type'],
                'type'          => $decl['type'][0],
                'alltypes'      => $decl['type'],
                'notnull'       => (bool) $val['notnull'],
                'default'       => $val['dflt_value'],
                'primary'       => (bool) $val['pk'],
                'length'        => null,
                'scale'         => null,
                'precision'     => null,
                'unsigned'      => null,
                'autoincrement' => (bool) ($val['pk'] == 1 && $decl['type'][0] == 'integer'),
            ];
            $columns[$val['name']] = $description;
        }
        return $columns;
    }
}
