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
            "sun2": "Do",
            "mon2": "Lu",
            "tue2": "Ma",
            "wed2": "Mi",
            "thu2": "Ju",
            "fri2": "Vi",
            "sat2": "Sa",
            "sun": "Dom",
            "mon": "Lun",
            "tue": "Mar",
            "wed": "Mie",
            "thu": "Jue",
            "fri": "Vie",
            "sat": "Sab",
            "sunday": "Sunday",
            "monday": "Monday",
            "tuesday": "Tuesday",
            "wednesday": "Wednesday",
            "thursday": "Thursday",
            "friday": "Friday",
            "saturday": "Saturday",
            "jan": "Ene",
            "feb": "Feb",
            "mar": "Mar",
            "apr": "Abr",
            "may": "May",
            "jun": "Jun",
            "jul": "Jul",
            "aug": "Ago",
            "sep": "Sep",
            "oct": "Oct",
            "nov": "Nov",
            "dec": "Dec",
            "l_jan": "Enero",
            "l_feb": "Febrero",
            "l_mar": "Marzo",
            "l_apr": "Abril",
            "l_may": "Mayo",
            "l_jun": "Junio",
            "l_jul": "Julio",
            "l_aug": "Agosto",
            "l_sep": "Septiembre",
            "l_oct": "Octubre",
            "l_nov": "Noviembre",
            "l_dec": "Diciembre"
        },
        "no_implemented": "No implementado aun",
        "to_date_view": "Click para ver la fecha actual",
        "i_undefined": "Indefinido",
        "allday_event": "Evento de todo el dia",
        "repeat_event": "Repetir evento",
        "time": "Hora",
        "event": "Evento",
        "location": "Ubicación",
        "participant": "Participante",
        "get_data_exception": "Error cargando datos",
        "new_event": "Nuevo evento",
        "confirm_delete_event": "Confirma que desea borrar este evento? ",
        "confrim_delete_event_or_all": "Desea borrar todos las repeticiones de este evento o solo este evento? \r\nClic [OK / Aceptar] para borrar solo este evento, clic [Cancel / Cancelar] para borrar todos los eventos.",
        "data_format_error": "Error de formato de datos! ",
        "invalid_title": "El título del evento no puede ser vacío o contener ($<>)",
        "view_no_ready": "La vista no esta lista aun",
        "example": "Ej., Evento en habitacion 107",
        "content": "Que",
        "create_event": "Crear evento",
        "update_detail": "Editar detalles",
        "click_to_detail": "Ver detalles",
        "i_delete": "Borrar",
        "i_save": "Salvar",
        "i_close": "Cerrar",
        "day_plural": "dias",
        "others": "Otros",
        "item": "",
        "loading_data":"Cargando datos...",
        "request_processed":"El pedido esta siendo procesado ...",
        "success":"Exitoso!",
        "are_you_sure_delete":"Esta seguro que desea borrar este evento",
        "ok":"Aceptar",
        "cancel":"Cancelar",
        "manage_the_calendar":"Administrar el Calendario",
        "error_occurs":"Han ocurrido errores",
        "color":"Color",
        "invalid_date_format":"Formato de fecha invalido",
        "invalid_time_format":"Formato de hora invalido",
        "_simbol_not_allowed":"$<> no estan permitidos",
        "subject":"Asunto",
        "time":"Hora",
        "to":"A",
        "all_day_event":"Evento de Todo el Día",
        "location":"Ubicación",
        "remark":"Descripción",
        "click_to_create_new_event":"Click para Crear Nuevo Evento",
        "new_event":"Nuevo Evento",
        "click_to_back_to_today":"Click para regresar a hoy",
        "today":"Hoy",
        "sday":"Día",
        "week":"Semana",
        "month":"Mes",
        "ndays":"Días",
        "list":"Lista",
        "nmonth":"nMes",
        "refresh_view":"Recargar vista",
        "refresh":"Recargar",
        "prev":"Prev",
        "next":"Sig",
        "loading":"Cargando",
        "error_overlapping":"This event is overlapping another event",
        "sorry_could_not_load_your_data":"No se ha podido cargar sus datos, por favor pruebe nuevamente",
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
        "weeks":"semanas",
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
        "days":"dias",
        "once":"Once",
        "times":"times",
        "readmore":"read more",
        "readmore_less":"less",
        "until":"until"
    }
});