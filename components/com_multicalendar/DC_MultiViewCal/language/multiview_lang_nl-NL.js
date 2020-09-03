var i18n = jQuery.extend({}, i18n || {}, {
    dcmvcal: {
        dateformat: {
            "fulldaykey": "ddMMyyyy",
            "fulldayshow": "d L yyyy",
            "fulldayvalue": "d/M/yyyy", 
            "Md": "W d/M",
            "nDaysView": "d/M",
            "listViewDate": "d L yyyy",
            "Md3": "d L",
            "separator": "/",
            "year_index": 2,
            "month_index": 1,
            "day_index": 0,
            "day": "d",
            "sun2": "Zo",
            "mon2": "Ma",
            "tue2": "Di",
            "wed2": "Wo",
            "thu2": "Do",
            "fri2": "Vr",
            "sat2": "Za",
            "sun": "Zon",
            "mon": "Maa",
            "tue": "Din",
            "wed": "Woe",
            "thu": "Don",
            "fri": "Vri",
            "sat": "Zat",
            "sunday": "Sunday",
            "monday": "Monday",
            "tuesday": "Tuesday",
            "wednesday": "Wednesday",
            "thursday": "Thursday",
            "friday": "Friday",
            "saturday": "Saturday",
            "jan": "Jan",
            "feb": "Feb",
            "mar": "Maa",
            "apr": "Apr",
            "may": "Mei",
            "jun": "Jun",
            "jul": "Jul",
            "aug": "Aug",
            "sep": "Sep",
            "oct": "Okt",
            "nov": "Nov",
            "dec": "dec",
            "l_jan": "Januari",
            "l_feb": "Februari",
            "l_mar": "Maart",
            "l_apr": "April",
            "l_may": "Mei",
            "l_jun": "Juni",
            "l_jul": "Juli",
            "l_aug": "Augustus",
            "l_sep": "September",
            "l_oct": "Oktober",
            "l_nov": "November",
            "l_dec": "december"
        },
        "no_implemented": "Nog niet geimplementeerd",
        "to_date_view": "Klik hier om de vandaags datum te zien",
        "i_undefined": "Onbepaald",
        "allday_event": "Evenement van een hele dag",
        "repeat_event": "Evenement herhalen",
        "time": "Tijd",
        "event": "Evenement",
        "location": "Plaats",
        "participant": "Deelnemer",
        "get_data_exception": "Fout bij het verkrijgen van gegevens",
        "new_event": "Nieuw evenement",
        "confirm_delete_event": "Bevestigt U het verwijderen van dit evenement?",
        "confrim_delete_event_or_all": "Wilt U alle herhaalde evenementen verwijderen, of alleen maar dit evenement? \r\n Klik op [OK] om alleen dit evenement te verwijderen, klik op [Annuleren] om alle herhaalde evenementen te verwijderen.",
        "data_format_error": "Fout met het formaat van de gegevens",
        "invalid_title": "Titel van het evenement kan niet leeg zijn of ($<>) bevatten",
        "view_no_ready": "Afbeelding is nog niet klaar",
        "example": "Bijv. Bijeenkomst in zaal 107",
        "content": "Wat",
        "create_event": "Maak een evenement",
        "update_detail": "Details wijzigen",
        "click_to_detail": "Details bekijken",
        "i_delete": "Verwijderen",
        "i_save": "Opslaan",
        "i_close": "Sluiten",
        "day_plural": "Dagen",
        "others": "Anderen",
        "item": "",
        "loading_data":"Gegevens worden geladen...",
        "request_processed":"Het verzoek wordt verwerkt....",
        "success":"Succes!",
        "are_you_sure_delete":"Bent U zeker dat U dit evenement wilt verwijderen?",
        "ok":"OK",
        "cancel":"Annuleren",
        "manage_the_calendar":"Agenda beheren",
        "error_occurs":"Fout",
        "color":"Kleur",
        "invalid_date_format":"Ongeldige datumsformaat",
        "invalid_time_format":"Ongeldige tijdsformaat",
        "_simbol_not_allowed":"$<> niet toegestaan",
        "subject":"Onderwerp",
        "time":"Tijd",
        "to":"tot",
        "all_day_event":"Evenement van een hele dag",
        "location":"Plaats",
        "remark":"Beschrijving",
        "click_to_create_new_event":"Klik hier om een nieuw evenement te maken",
        "new_event":"Nieuw evenement",
        "click_to_back_to_today":"Klik hier om terug te gaan naar vandaag",
        "today":"Vandaag",
        "sday":"Dag",
        "week":"Week",
        "month":"Maand",
        "ndays":"Dag",
        "list":"List",
        "nmonth":"nMaand",
        "refresh_view":"Uitzicht vernieuwen",
        "refresh":"Verversen",
        "prev":"Vorige",
        "next":"Volg.",
        "loading":"Uw gegevens worden geladen.",
        "error_overlapping":"This event is overlapping another event",
        "sorry_could_not_load_your_data":"Uw gegevens kunnen helaas niet geladen worden,  probeer het later nog eens.",
        "first":"First",
        "second":"Second",
        "third":"Third",
        "fourth":"Fourth",
        "last":"last",
        "repeat":"Repeat: ",
        "edit":"Edit",
        "edit_recurring_event":"Edit recurring event",
        "would_you_like_to_change_only_this_event_all_events_in_the_series_or_this_and_all_following_events_in_the_series":"Would you like to change only this event, all events in the series, or this and all following events in the series?",
        "only_this_event":"Only this event",
        "all_other_events_in_the_series_will_remain_the_same":"All other events in the series will remain the same.",
        "following_events":"Following events",
        "this_and_all_the_following_events_will_be_changed":"This and all the following events will be changed.",
        "any_changes_to_future_events_will_be_lost":"Any changes to future events will be lost.",
        "all_events":"All events",
        "all_events_in_the_series_will_be_changed":"All events in the series will be changed.",
        "any_changes_made_to_other_events_will_be_kept":"Any changes made to other events will be kept.",
        "cancel_this_change":"Cancel this change",
        "delete_recurring_event":"Delete recurring event",
        "would_you_like_to_delete_only_this_event_all_events_in_the_series_or_this_and_all_future_events_in_the_series":"Would you like to delete only this event, all events in the series, or this and all future events in the series?",
        "only_this_instance":"Only this instance",
        "all_other_events_in_the_series_will_remain":"All other events in the series will remain.",
        "all_following":"All following",
        "this_and_all_the_following_events_will_be_deleted":"This and all the following events will be deleted.",
        "all_events_in_the_series":"All events in the series",
        "all_events_in_the_series_will_be_deleted":"All events in the series will be deleted.",
        "repeats":"Repeats",
        "daily":"Daily",
        "every_weekday_monday_to_friday":"Every weekday (Monday to Friday)",
        "every_monday_wednesday_and_friday":"Every Monday, Wednesday, and Friday",
        "every_tuesday_and_thursday":"Every Tuesday, and Thursday",
        "weekly":"Weekly",
        "monthly":"Monthly",
        "yearly":"Yearly",
        "repeat_every":"Repeat every:",
        "weeks":"weeks",
        "repeat_on":"Repeat on:",
        "repeat_by":"Repeat by:",
        "day_of_the_month":"day of the month",
        "day_of_the_week":"day of the week",
        "starts_on":"Starts on:",
        "ends":"Ends:",
        "never":" Never",
        "after":"After",
        "occurrences":"occurrences",
        "summary":"Summary:",
        "every":"Every",
        "weekly_on_weekdays":"Weekly on weekdays",
        "weekly_on_monday_wednesday_friday":"Weekly on Monday, Wednesday, Friday",
        "weekly_on_tuesday_thursday":"Weekly on Tuesday, Thursday",
        "on":"on",
        "on_day":"on day",
        "on_the":"on the",
        "months":"months",
        "annually":"Annually",
        "years":"years",
        "days":"days",
        "once":"Once",
        "times":"times",
        "readmore":"read more",
        "readmore_less":"less",
        "until":"until"
    }
});
