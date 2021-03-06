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
            "sun2": "Sø",
            "mon2": "Ma",
            "tue2": "Ti",
            "wed2": "On",
            "thu2": "To",
            "fri2": "Fr",
            "sat2": "Sa",
            "sun": "Søn",
            "mon": "Man",
            "tue": "Tir",
            "wed": "Ons",
            "thu": "Tor",
            "fri": "Fre",
            "sat": "Lør",
            "sunday": "Sunday",
            "monday": "Monday",
            "tuesday": "Tuesday",
            "wednesday": "Wednesday",
            "thursday": "Thursday",
            "friday": "Friday",
            "saturday": "Saturday",
            "jan": "Jan",
            "feb": "Feb",
            "mar": "Mar",
            "apr": "Apr",
            "may": "May",
            "jun": "Jun",
            "jul": "Jul",
            "aug": "Aug",
            "sep": "Sep",
            "oct": "Oct",
            "nov": "Nov",
            "dec": "Dec",
            "l_jan": "Januar",
            "l_feb": "Februar",
            "l_mar": "Mars",
            "l_apr": "April",
            "l_may": "Mai",
            "l_jun": "Juni",
            "l_jul": "Juli",
            "l_aug": "August",
            "l_sep": "September",
            "l_oct": "October",
            "l_nov": "November",
            "l_dec": "Desember"
        },
        "no_implemented": "Ikke innført",
        "to_date_view": "Trykk for å vise dato",
        "i_undefined": "Udefinert",
        "allday_event": "Hele dagen",
        "repeat_event": "Repeter hendelse",
        "time": "Tid",
        "event": "Event",
        "location": "Sted",
        "participant": "Deltager",
        "get_data_exception": "Exception when getting data",
        "new_event": "Ny oppføring",
        "confirm_delete_event": "Vil du slette? ",
        "confrim_delete_event_or_all": "Do you want to delete all repeat events or only this event? \r\nClick [OK] to delete only this event, click [Cancel] delete all events",
        "data_format_error": "Data format error! ",
        "invalid_title": "Event title can not be blank or contains ($<>)",
        "view_no_ready": "View is not ready",
        "example": "eks. Møterom 14",
        "content": "Hva",
        "create_event": "Lag oppføring",
        "update_detail": "Endre info",
        "click_to_detail": "Vis info",
        "i_delete": "Slett",
        "i_save": "Lagre",
        "i_close": "Lukk",
        "day_plural": "dager",
        "others": "Andre",
        "item": "Objekt",
        "loading_data":"Laster data...",
        "request_processed":"The request is being processed ...",
        "success":"Suksess!",
        "are_you_sure_delete":"Er du sikker du vil slette?",
        "ok":"Ok",
        "cancel":"Avbryt",
        "manage_the_calendar":"Redigere",
        "error_occurs":"Oppstått problem",
        "color":"Farge",
        "invalid_date_format":"Ugylding dato",
        "invalid_time_format":"Ugyldig tid",
        "_simbol_not_allowed":"$<> ikke tilatt",
        "subject":"Emne",
        "time":"Tid",
        "to":"Til",
        "all_day_event":"Hele dagen",
        "location":"Sted",
        "remark":"Beskrivelse",
        "click_to_create_new_event":"Trykk for ny oppføring",
        "new_event":"Ny oppføring",
        "click_to_back_to_today":"Tilbake til i dag",
        "today":"I dag",
        "sday":"Dag",
        "week":"Uke",
        "month":"Måned",
        "ndays":"Dager",
        "list":"List",
        "nmonth":"Fremover",
        "refresh_view":"Refresh visning",
        "refresh":"Refresh",
        "prev":"Forrige",
        "next":"Neste",
        "loading":"Loader",
        "error_overlapping":"Denne overlapper en annen oppføring",
        "sorry_could_not_load_your_data":"Beklager, kunne ikke laste data, prøv igjen senere",
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
