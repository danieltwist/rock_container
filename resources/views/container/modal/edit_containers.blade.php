<div class="modal fade" id="edit_containers_list">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Редактировать выбранные контейнеры</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('edit_containers_list') }}" id="edit_containers_form" method="POST">
                @csrf
                <input type="hidden" id="containers_list" name="containers_list">
                <div class="modal-body">
                    <button class="btn btn-sm btn-default" id="show_edit_containers_list" type="button" data-toggle="collapse" data-target="#containers_list_collapse" aria-expanded="false" aria-controls="containers_list_collapse">
                        Показать список редактируемых контейнеров
                    </button>
                    <div class="collapse" id="containers_list_collapse">
                        <div id="edit_containers_list_prefix" class="card card-body"></div>
                    </div>
                    <div class="card card-primary card-outline card-outline-tabs mt-4">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-four-static_data-tab" data-toggle="pill" href="#custom-tabs-four-static_data" role="tab" aria-controls="custom-tabs-four-static_data" aria-selected="true">Основные данные</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-four-supplier-tab" data-toggle="pill" href="#custom-tabs-four-supplier" role="tab" aria-controls="custom-tabs-four-supplier" aria-selected="false">Поставщик</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-four-relocation-tab" data-toggle="pill" href="#custom-tabs-four-relocation" role="tab" aria-controls="custom-tabs-four-relocation" aria-selected="false">Подсыл</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-four-client-tab" data-toggle="pill" href="#custom-tabs-four-client" role="tab" aria-controls="custom-tabs-four-client" aria-selected="false">Клиент</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade show active" id="custom-tabs-four-static_data" role="tabpanel" aria-labelledby="custom-tabs-four-static_data-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[status]"
                                                       id="status_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="status_null">
                                                    Статус
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2" name="status" id="status"
                                                        data-placeholder="Статус" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="В пути">В пути</option>
                                                    <option value="Перемещение">Перемещение</option>
                                                    <option value="К выдаче">К выдаче</option>
                                                    <option value="Заблокирован">Заблокирован</option>
                                                    <option value="Хранение">Хранение</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[type]"
                                                       id="type_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="type_null">
                                                    Тип
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2" name="type" id="type"
                                                        data-placeholder="Тип" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Соб">Соб</option>
                                                    <option value="Аренда">Аренда</option>
                                                    <option value="ОУ">ОУ</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="null_array[size]"
                                               id="size_null"
                                               value="yes">
                                        <label class="custom-control-label" for="size_null">
                                            Размер
                                        </label>
                                    </div>
                                    <div class="form-group mt-2">
                                        <select class="form-control select2" name="size" id="size"
                                                data-placeholder="Размер" style="width: 100%;" >
                                            <option></option>
                                            <option value="40HC">40HC</option>
                                            <option value="20DC">20DC</option>
                                            <option value="40OT">40OT</option>
                                            <option value="20OT">20OT</option>
                                            <option value="40DC">40DC</option>
                                            <option value="40RF">40RF</option>
                                        </select>
                                    </div>
                                    <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="null_array[additional_info]"
                                               id="additional_info_null"
                                               value="yes">
                                        <label class="custom-control-label" for="additional_info_null">
                                            {{ __('general.additional_info') }}
                                        </label>
                                    </div>
                                    <div class="form-group mt-2">
                                        <textarea class="form-control to_uppercase" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}"></textarea>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-four-supplier" role="tabpanel" aria-labelledby="custom-tabs-four-supplier-tab">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_date_get]"
                                                       id="supplier_date_get_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_date_get_null">
                                                    Дата приема
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input" name="supplier_date_get"
                                                       placeholder="Дата приема">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_date_start_using]"
                                                       id="supplier_date_start_using_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_date_start_using_null">
                                                    Начало пользования
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input" name="supplier_date_start_using"
                                                       placeholder="Начало пользования">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[svv]"
                                                       id="svv_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="svv_null">
                                                    СВВ
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input" name="svv"
                                                       placeholder="СВВ">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_terminal]"
                                                       id="supplier_terminal_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_terminal_null">
                                                    Терминал
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control"
                                                       name="supplier_terminal"
                                                       placeholder="Терминал">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_terminal_storage]"
                                                       id="supplier_terminal_storage_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_terminal_storage_null">
                                                    Терминальное хранение
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control rate_input"
                                                       name="supplier_terminal_storage_amount"
                                                       placeholder="Терминальное хранение">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Валюта</label>
                                                <select class="form-control select2" name="supplier_terminal_storage_currency"
                                                        data-placeholder="Выберите валюту" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="RUB">RUB</option>
                                                    <option value="USD">USD</option>
                                                    <option value="CNY">CNY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="null_array[supplier_payer_tx]"
                                               id="supplier_payer_tx_null"
                                               value="yes">
                                        <label class="custom-control-label" for="supplier_payer_tx_null">
                                            Плательщик ТХ
                                        </label>
                                    </div>
                                    <div class="form-group mt-2">
                                        <select class="form-control select2" name="supplier_payer_tx"
                                                data-placeholder="Плательщик ТХ" style="width: 100%;" >
                                            <option></option>
                                            <option value="Собственник">Собственник</option>
                                            <option value="РК">РК</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_renewal_reexport_costs]"
                                                       id="supplier_renewal_reexport_costs_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_renewal_reexport_costs_null">
                                                    Расходы за продления/реэкспорт
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control rate_input"
                                                       name="supplier_renewal_reexport_costs_amount"
                                                       placeholder="Расходы за продления/реэкспорт">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Валюта</label>
                                                <select class="form-control select2" name="supplier_renewal_reexport_costs_currency"
                                                        data-placeholder="Выберите валюту" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="RUB">RUB</option>
                                                    <option value="USD">USD</option>
                                                    <option value="CNY">CNY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_repair]"
                                                       id="supplier_repair_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_repair_null">
                                                    Ремонт при получении ктк
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control rate_input"
                                                       name="supplier_repair_amount"
                                                       placeholder="Ремонт при получении ктк">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Валюта</label>
                                                <select class="form-control select2"
                                                        name="supplier_repair_currency"
                                                        data-placeholder="Выберите валюту" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="RUB">RUB</option>
                                                    <option value="USD">USD</option>
                                                    <option value="CNY">CNY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_repair_status]"
                                                       id="supplier_repair_status_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_repair_status_null">
                                                    Статус ремонта
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="supplier_repair_status"
                                                        data-placeholder="Статус ремонта" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Отремонтирован">Отремонтирован</option>
                                                    <option value="Целый">Целый</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[supplier_repair_confirmation]"
                                                       id="supplier_repair_confirmation_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="supplier_repair_confirmation_null">
                                                    Подтверждение ремонта собствеником
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="supplier_repair_confirmation"
                                                        data-placeholder="Подтверждение ремонта собствеником" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Нет">Нет</option>
                                                    <option value="Да">Да</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-four-relocation" role="tabpanel" aria-labelledby="custom-tabs-four-relocation-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[relocation_date_send]"
                                                       id="relocation_date_send_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="relocation_date_send_null">
                                                    Дата отправки ктк
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input"
                                                       name="relocation_date_send"
                                                       placeholder="Дата отправки ктк">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[relocation_date_arrival_to_terminal]"
                                                       id="relocation_date_arrival_to_terminal_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="relocation_date_arrival_to_terminal_null">
                                                    Дата сдачи на терминал
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input"
                                                       name="relocation_date_arrival_to_terminal"
                                                       placeholder="Дата сдачи на терминал">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[relocation_place_of_delivery_terminal]"
                                                       id="relocation_place_of_delivery_terminal_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="relocation_place_of_delivery_terminal_null">
                                                    Терминал
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control to_uppercase"
                                                       name="relocation_place_of_delivery_terminal"
                                                       placeholder="Терминал">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[relocation_delivery_time_days]"
                                                       id="relocation_delivery_time_days_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="relocation_delivery_time_days_null">
                                                    Нормативный срок доставки дней
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control digits_only"
                                                       name="relocation_delivery_time_days"
                                                       placeholder="Нормативный срок доставки дней">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[relocation_repair]"
                                                       id="relocation_repair_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="relocation_repair_null">
                                                    Ремонт при возврате ктк
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control rate_input"
                                                       name="relocation_repair_amount"
                                                       placeholder="Ремонт при возврате ктк">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Валюта</label>
                                                <select class="form-control select2"
                                                        name="relocation_repair_currency"
                                                        data-placeholder="Выберите валюту" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="RUB">RUB</option>
                                                    <option value="USD">USD</option>
                                                    <option value="CNY">CNY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[relocation_repair_status]"
                                                       id="relocation_repair_status_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="relocation_repair_status_null">
                                                    Статус ремонта
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="relocation_repair_status"
                                                        data-placeholder="Статус ремонта" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Отремонтирован">Отремонтирован</option>
                                                    <option value="Целый">Целый</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[relocation_repair_confirmation]"
                                                       id="relocation_repair_confirmation_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="relocation_repair_confirmation_null">
                                                    Подтверждение ремонта клиентом
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="relocation_repair_confirmation"
                                                        data-placeholder="Подтверждение ремонта клиентом" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Нет">Нет</option>
                                                    <option value="Да">Да</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-four-client" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_date_get]"
                                                       id="client_date_get_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_date_get_null">
                                                    Дата выдачи Клиенту
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input"
                                                       name="client_date_get"
                                                       placeholder="Дата выдачи Клиенту">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_date_return]"
                                                       id="client_date_return_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_date_return_null">
                                                    Дата возврата ктк
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input"
                                                       name="client_date_return"
                                                       placeholder="Дата возврата ктк">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_repair]"
                                                       id="client_repair_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_repair_null">
                                                    Ремонт при возврате ктк
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control rate_input"
                                                       name="client_repair_amount"
                                                       placeholder="Ремонт при возврате ктк">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Валюта</label>
                                                <select class="form-control select2"
                                                        name="client_repair_currency"
                                                        data-placeholder="Выберите валюту" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="RUB">RUB</option>
                                                    <option value="USD">USD</option>
                                                    <option value="CNY">CNY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_repair_status]"
                                                       id="client_repair_status_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_repair_status_null">
                                                    Статус ремонта
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="client_repair_status"
                                                        data-placeholder="Статус ремонта" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Отремонтирован">Отремонтирован</option>
                                                    <option value="Целый">Целый</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_repair_confirmation]"
                                                       id="client_repair_confirmation_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_repair_confirmation_null">
                                                    Подтверждение ремонта клиентом
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="client_repair_confirmation"
                                                        data-placeholder="Подтверждение ремонта клиентом" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Нет">Нет</option>
                                                    <option value="Да">Да</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_smgs]"
                                                       id="client_smgs_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_smgs_null">
                                                    СМГС
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="client_smgs"
                                                        data-placeholder="СМГС" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Нет">Нет</option>
                                                    <option value="Запрошено">Запрошено</option>
                                                    <option value="Загружено">Загружено</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_manual]"
                                                       id="client_manual_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_manual_null">
                                                    Инструкция
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="client_manual"
                                                        data-placeholder="Инструкция" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Нет">Нет</option>
                                                    <option value="Запрошено">Запрошено</option>
                                                    <option value="Предоставлено">Предоставлено</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_date_manual_request]"
                                                       id="client_date_manual_request_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_date_manual_request_null">
                                                    Дата запроса инструкции
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" class="form-control date_input"
                                                       name="client_date_manual_request"
                                                       placeholder="Дата запроса инструкции">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="null_array[client_return_act]"
                                                       id="client_return_act_null"
                                                       value="yes">
                                                <label class="custom-control-label" for="client_return_act_null">
                                                    Акт сдачи
                                                </label>
                                            </div>
                                            <div class="form-group mt-2">
                                                <select class="form-control select2"
                                                        name="client_return_act"
                                                        data-placeholder="Акт сдачи" style="width: 100%;" >
                                                    <option></option>
                                                    <option value="Нет">Нет</option>
                                                    <option value="Запрошено">Запрошено</option>
                                                    <option value="Загружено">Загружено</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-danger">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="null_array[client_location_request]"
                                               id="client_location_request_null"
                                               value="yes">
                                        <label class="custom-control-label" for="client_location_request_null">
                                            Локация запроса
                                        </label>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="text" class="form-control to_uppercase"
                                               name="client_location_request"
                                               placeholder="Локация запроса">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
{{--                    <button type="submit" class="btn btn-primary" id="save_containers_list_edits"--}}
{{--                            data-action='{"hide_modal":{"id": "edit_containers_list"}, "update_table":{"table_id":"containers_extended_ajax_table", "type":"ajax"},"reset_form":{"need_reset": "true"}}'>--}}
{{--                        {{ __('general.save') }}--}}
{{--                    </button>--}}
                    <button type="submit" class="btn btn-primary" id="save_containers_list_edits"
                            data-action='{"update_table":{"table_id":"containers_extended_ajax_table", "type":"ajax"}}'>
                        {{ __('general.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
