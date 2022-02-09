package org.vufind.index;
/**
 * CLB - Custom date functions (based on UpdateDateTools).
 *
 * Copyright (C) Villanova University 2017.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

import java.time.format.DateTimeFormatter;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.ZoneOffset;
import java.util.Iterator;
import java.util.Set;
import org.solrmarc.index.SolrIndexer;
import org.marc4j.marc.Record;

public class CLB_DateTools
{
    private DateTimeFormatter marc005date = DateTimeFormatter.ofPattern("yyyyMMddHHmmss.S");
    private DateTimeFormatter marc008date = DateTimeFormatter.ofPattern("yyMMdd");
    private DateTimeFormatter formatter = DateTimeFormatter.ISO_DATE_TIME;

    //private LocalDateTime normalize005Date(String input)
    private String normalize005Date(String input)
    {
        if (input == null) {
            input = "null";
        }

        LocalDateTime retVal;
        try {
            retVal = LocalDateTime.parse(input, marc005date);
        } catch(java.time.format.DateTimeParseException e) {
            retVal = LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC);
        }
        return retVal.format(formatter);
    }

    //private LocalDateTime normalize008Date(String input)
    private String normalize008Date(String input)
    {
        if (input == null || input.length() < 6) {
            input = "null";
        }

        LocalDateTime retVal;
        try {
            retVal = LocalDate.parse(input.substring(0, 6), marc008date).atStartOfDay();
        } catch(java.lang.StringIndexOutOfBoundsException | java.time.format.DateTimeParseException e) {
            retVal = LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC);
        }
        return retVal.format(formatter);
    }

    /**
     * Extract the latest transaction date from the MARC record.
     * @param record MARC record
     * @return Latest transaction date.
     */
    //public LocalDateTime CLB_getLatestTransactionDate(Record record) {
    public String CLB_getLatestTransactionDate(Record record) {
     
        Set<String> dates = SolrIndexer.instance().getFieldList(record, "005");
        if (dates != null) {
            Iterator<String> dateIter = dates.iterator();
            if (dateIter.hasNext()) {
                return normalize005Date(dateIter.next());
            }
        }

        return LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC).format(formatter);
    }

    /**
     * Extract the creation date from the MARC record.
     * @param record MARC record
     * @return Creation date.
     */
    //public LocalDateTime CLB_getCreationDate(Record record) {
    public String CLB_getCreationDate(Record record) {
        Set<String> dates = SolrIndexer.instance().getFieldList(record, "008");
        if (dates != null) {
            Iterator<String> dateIter = dates.iterator();
            if (dateIter.hasNext()) {
                return normalize008Date(dateIter.next());
            }
        }

        return LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC).format(formatter);
    }

}

