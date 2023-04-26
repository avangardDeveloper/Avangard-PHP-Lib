<?php

namespace Box;

interface GenerateBoxAuth
{
    const INPUT_TYPE_TEXT = 'text';
    const INPUT_TYPE_SELECT = 'select';
    const INPUT_TYPE_FILE = 'file';

    /**
     * Функция для получения всех параметров, необходимых для
     * инициализации объекта кассы. Возвращает массив для
     * создания веб-формы на фронтенде, которую должен заполнить
     * пользователь.
     * <br><br>
     * Нужно вернуть массив вида:<br>
     * [
     *      box => [
     *          type => Тип кассы (выбирается из констант класса BaseBox)
     *          name => Семантическое название кассы
     *      ],
     *      <название поля (HTML атрибут input name)> => [
     *          type => Тип поля (выбирается из констант данного класса - text, select, и т.д.)
     *          placeholder => Подсказка для поля
     *          validation => Подсказка, если поле не заполнено
     *          options => Варианты выбора для поля типа select.
     *                     Массив вида <option value> => <option text>.
     *                     Должен возвращаться методом из класса кассы, который реализует интерфейс GenerateBox
     *      ],
     *      <остальные поля по примеру выше>
     * ]
     *
     * @return array
     */
    public static function getAuthParams();
}