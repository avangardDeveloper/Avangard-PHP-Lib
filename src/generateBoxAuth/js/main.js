export default class MagicForm {
    constructor(boxData = {}) {
        this.boxData = this.parseBoxData(boxData);
        this.boxFields = this.parseBoxFields();
        this.render();
    }

    parseBoxData(boxData) {
        return boxData.reduce((accum, item) => {
            const {type, name} = item.box;
            delete item.box;
            accum[ type ] = {
                name: name,
                fields: item,
            };

            return accum;
        }, {});
    }

    parseBoxFields() {
        return Object.entries(this.boxData).reduce((accum, [boxType, boxData]) => {
            accum[boxType] = this.getBoxFields(boxData.fields);

            return accum;
        }, {});
    }

    getBoxFields(boxFields) {
        return Object.entries(boxFields).reduce((accum, [fieldName, fieldData]) => {
            accum += this.getBoxField(fieldName, fieldData);

            return accum;
        }, '');
    }

    getBoxField(fieldName, fieldData) {
        return (fieldData.type !== undefined)
            ? this.getBoxFieldTemplate(fieldName, fieldData)
            : Object.entries(fieldData).reduce((accum, [subFieldName, subFieldData]) => {
                accum += this.getBoxFieldTemplate(fieldName, subFieldData, subFieldName);

                return accum;
            }, '');
    }

    getBoxFieldTemplate(fieldName, fieldData, subFieldName = null) {
        const fieldId = subFieldName ?? fieldName;
        const inputName = subFieldName !== null ? `${fieldName}[${subFieldName}]` : fieldName;

        return `<div id="${fieldId}" class="wrap-input100 validate-input m-b-35" data-validate="${fieldData.validation ?? ''}">
                    ${this.getBoxInput(inputName, fieldData)}
                    <span class="focus-input100" data-placeholder="${fieldData.placeholder}"></span>
                </div>`;
    }

    getBoxInput(inputName, fieldData) {
        switch (fieldData.type) {
            case 'text':
                return this.getTextField(inputName);
            case 'select':
                return this.getSelectField(inputName, fieldData.options);
            case 'file':
                return this.getFileField(inputName);
            default:
                return '';
        }
    }

    getTextField(inputName) {
        return `<input class="input100" type="text" name="${inputName}">`;
    }

    getSelectField(inputName, options) {
        return `<select class="input100" name="${inputName}">
                    <option value=""></option>
                    ${Object.entries(options).reduce((accum, [optionValue, optionTitle]) => {
                        accum += `<option  value="${optionValue}">${optionTitle}</option>`;
                        return accum;
                    }, '')}
                </select>`;
    }

    getFileField(inputName) {
        return `<div class="input100_mock"></div>
                <input class="input100" type="file" name="${inputName}">`;
    }

    render() {
        const element = document.createElement('div');

        element.innerHTML = this.template;

        this.element = element.firstElementChild;
        this.subElements = this.getSubElements(this.element);

        this.initEventListeners();
    }

    get template() {
        return `<form id="magic-form" method="post" class="login100-form validate-form" action="ajax.php">
                    <div class="wrap-input100 validate-input m-b-35" data-validate="Выберете тип кассы">
                        <select id="boxType" class="input100" name="boxType" data-element="boxTypeSelect">
                            <option value=""></option>
                            ${Object.entries(this.boxData).reduce((accum, [boxType, boxData]) => {
                                accum += `<option value ="${boxType}">${boxData.name}</option>`;
                    
                                return accum;
                            }, '')}
                        </select>
                        <span class="focus-input100" data-placeholder="Касса"></span>
                    </div>
                    <div data-element="boxFields"></div>
                    <div class="container-login100-form-btn">
                        <button data-element="submitButton" class="login100-form-btn">Сгенерировать</button>
                    </div>
                </form>`;
    }

    getSubElements(element) {
        const elements = element.querySelectorAll('[data-element]');

        return [...elements].reduce((accum, subElement) => {
            accum[subElement.dataset.element] = subElement;

            return accum;
        }, {});
    }

    getResultContainer(result) {
        return `<div class="wrap-input100 result">${result}</div>`;
    }

    initEventListeners() {
        this.element.addEventListener('input', this.onChangeInput);
        this.element.addEventListener('change', this.onChangeInput);
        this.element.addEventListener('change', this.onAddInputFile);
        this.element.addEventListener('click', this.inputMockBehavior);
        this.subElements.boxTypeSelect.addEventListener('change', this.showFormFields);
        this.subElements.submitButton.addEventListener('click', this.generateBoxAuth);
    }

    onChangeInput = event => {
        const target = event.target;

        if (target.classList.contains('input100')) {
            if (target.value !== '') {
                target.classList.add('has-val');
                target.parentElement.classList.remove('alert-validate');
            } else {
                target.classList.remove('has-val');
            }
        }
    }

    inputMockBehavior = event => {
        const target = event.target;
        if (target.classList.contains('input100_mock')) {
            target.parentElement.querySelector('.input100').click();
        }
    }

    onAddInputFile = event => {
        const target = event.target;

        if (target.type === 'file') {
            const inputMock = target.parentElement.querySelector('.input100_mock');

            if (target.files.length > 0) {
                inputMock.innerHTML = target.files[0].name;
                inputMock.classList.add('has-val');
            } else {
                inputMock.innerHTML = '';
                inputMock.classList.remove('has-val');
            }
        }
    }

    showFormFields = (event) => {
        this.subElements.boxFields.innerHTML = this.boxFields[ event.target.value ] ?? '';
    }

    generateBoxAuth = async (event) => {
        event.preventDefault();

        let valid = true;
        const inputs = this.element.querySelectorAll('.input100');
        for (const input of inputs) {
            if (input.value === '') {
                input.parentElement.classList.add('alert-validate');
                valid = false;
            }
        }

        if (!valid) return false;

        const formData = new FormData(this.element);
        const response = await fetch(this.element.action, {
            method: 'POST',
            body: formData,
        });
        const result = await response.json();
        console.log(result);

        this.element.innerHTML = this.getResultContainer(JSON.stringify(result));
    }

    remove() {
        this.element.remove();
    }

    destroy() {
        this.remove();
    }
}

// (function ($) {
//     "use strict";
//
//
//     /*==================================================================
//     [ Focus input ]*/
//     $('.input100').each(function(){
//         $(this).on('blur', function(){
//             if($(this).val().trim() != "") {
//                 $(this).addClass('has-val');
//             }
//             else {
//                 $(this).removeClass('has-val');
//             }
//         })
//     })
//
//
//     /*==================================================================
//     [ Validate ]*/
//     var input = $('.validate-input .input100');
//
//     $('.validate-form').on('submit',function(){
//         var check = true;
//
//         for(var i=0; i<input.length; i++) {
//             if(validate(input[i]) == false){
//                 showValidate(input[i]);
//                 check=false;
//             }
//         }
//
//         return check;
//     });
//
//
//     $('.validate-form .input100').each(function(){
//         $(this).focus(function(){
//            hideValidate(this);
//         });
//     });
//
//     function validate (input) {
//         if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
//             if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
//                 return false;
//             }
//         }
//         else {
//             if($(input).val().trim() == ''){
//                 return false;
//             }
//         }
//     }
//
//     function showValidate(input) {
//         var thisAlert = $(input).parent();
//
//         $(thisAlert).addClass('alert-validate');
//     }
//
//     function hideValidate(input) {
//         var thisAlert = $(input).parent();
//
//         $(thisAlert).removeClass('alert-validate');
//     }
//
//
// })(jQuery);