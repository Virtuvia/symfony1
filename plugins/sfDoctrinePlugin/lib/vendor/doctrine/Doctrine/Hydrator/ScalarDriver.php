<?php
/*
 *  $Id$
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
 * Builds result sets in to a scalar php array
 *
 * @package     Doctrine
 * @subpackage  Hydrate
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Hydrator_ScalarDriver extends Doctrine_Hydrator_Abstract
{
    public function hydrateResultSet($stmt)
    {
        $cache = [];
        $result = [];

        while ($data = $stmt->fetch(Doctrine_Core::FETCH_ASSOC)) {
            $result[] = $this->_gatherRowData($data, $cache);
        }

        return $result;
    }

    protected function _gatherRowData($data, &$cache, $aliasPrefix = true)
    {
        $rowData = [];
        foreach ($data as $key => $value) {
            // Parse each column name only once. Cache the results.
            $cache[$key] ??= $this->buildColumnCache($key);

            $table = $this->_queryComponents[$cache[$key]['dqlAlias']]['table'];
            $dqlAlias = $cache[$key]['dqlAlias'];
            $fieldName = $cache[$key]['fieldName'];

            $rowDataKey = $aliasPrefix ? $dqlAlias . '_' . $fieldName : $fieldName;

            $rowData[$rowDataKey] = $cache[$key]['type'] ? $table->convertToPhpValue($fieldName, $value, $cache[$key]['type']) : $value;
        }
        return $rowData;
    }
}
