<?php

/**
 * Kolab storage cache class for note objects
 *
 * @author Aleksander Machniak <machniak@apheleia-it.ch>
 *
 * Copyright (C) 2013-2022 Apheleia IT AG <contact@apheleia-it.ch>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class kolab_storage_dav_cache_note extends kolab_storage_dav_cache
{
    protected $data_props = ['categories', 'links', 'title'];
    protected $fulltext_cols = ['categories', 'title', 'description'];

    /**
     * Helper method to convert the given Kolab object into a dataset to be written to cache
     *
     * @override
     */
    protected function _serialize($object)
    {
        $sql_data = parent::_serialize($object);

        $sql_data['tags'] = ' ' . implode(' ', $this->get_tags($object)) . ' ';
        $sql_data['words'] = ' ' . implode(' ', $this->get_words($object)) . ' ';

        return $sql_data;
    }
    /**
     * Callback for kolab_storage_cache to get object specific tags to cache
     *
     * @return array List of tags to save in cache
     */
    public function get_tags($object)
    {
        $tags = [];

        foreach ((array)($object['categories'] ?? null) as $cat) {
            $tags[] = rcube_utils::normalize_string($cat);
        }

        // add tag for message references
        foreach ((array)($object['links'] ?? []) as $link) {
            $url = parse_url(str_replace(':///', '://', $link));
            if ($url['scheme'] == 'imap') {
                parse_str($url['query'], $param);
                $tags[] = 'ref:' . trim($param['message-id'] ?: urldecode($url['fragment']), '<> ');
            }
        }

        return $tags;
    }

    /**
     * Callback to get words to index for fulltext search
     *
     * @return array List of words to save in cache
     */
    public function get_words($object = [])
    {
        $data = '';

        foreach ($this->fulltext_cols as $col) {
            if (empty($object[$col])) {
                continue;
            }

            // convert HTML content to plain text
            if ($col == 'description'
                && preg_match('/<(html|body)(\s[a-z]|>)/', $object[$col], $m)
                && strpos($object[$col], '</' . $m[1] . '>')
            ) {
                $converter = new rcube_html2text($object[$col], false, false, 0);
                $val = $converter->get_text();
            } else {
                $val = is_array($object[$col]) ? implode(' ', $object[$col]) : $object[$col];
            }

            if (is_string($val) && strlen($val)) {
                $data .= $val . ' ';
            }
        }

        $words = rcube_utils::normalize_string($data, true);

        return array_unique($words);
    }
}
