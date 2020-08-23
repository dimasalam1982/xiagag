'use strict';

/**
 * Class requires jQuery as $
 * @type {{variantTrClass: string, plusBtnClass: string, tableClass: string}}
 */

const IDENTITIES = {
    variantTrClass: 'variantItem',
    tableClass: 'poll-table',
    plusBtnClass: 'btn--plus'
};

class Variant
{
    title = '';
    number = null;
    content;

    constructor(number) {
        this.number = number;
    }

    setTitle(title)
    {
        this.title = title;
    }

    validate()
    {
        validateField(this.content.find('.title-item'));
    }

    getTemplate()
    {
        const content = $(`<tr class="${IDENTITIES.variantTrClass}" data-num="${this.number}">
                    <th>Answer ${this.number}:</th>
                    <td>
                        <input type="text" value="${this.title}" class="input-text title-item">
                    </td>
                </tr>`);

        content.find('input').change( (event) => {
            validateField($(event.target));
            this.setTitle($(event.target).val());
        } );

        this.content = content;

        return content;
    }

    toBackendModel()
    {
        return {
            title: this.title
        };
    }
}

class Question
{
    title = null;
    variants = [];
    count = 0;
    blockSelector;
    content;
    questionUrl;

    constructor(blockSelector) {
        this.blockSelector = blockSelector;
        this.addVariant(new Variant(1));
        this.addVariant(new Variant(2));
        this.count = 2;
    }

    addVariant(variant)
    {
        this.variants.push(variant);
    }

    createVariant()
    {
        this.count++;
        const variant = new Variant(this.count);
        this.addVariant(variant);
        this.renderNewVariant(variant);
    }

    createPlusButton()
    {
        return `<tr>
                <td class="poll-table__plus">
                    <button class="btn ${IDENTITIES.plusBtnClass}">
                        +
                    </button>
                </td>
                <td> </td>
            </tr>`;
    }

    renderNewVariant(variant)
    {
        $(this.blockSelector).find(`.${IDENTITIES.tableClass} tr.${IDENTITIES.variantTrClass}:last()`).after(variant.getTemplate());
    }

    createStartButton()
    {
        const button = $(`<button class="btn btn--start">Start</button>`);

        button.click( () => {
            if (this.validate()){
                this.send();
            }
        } );

        return button;
    }

    render()
    {
        const content = $(`
            <table class="${IDENTITIES.tableClass}">
                <thead>
                <tr>
                    <th>Question:</th>
                    <th>
                        <input type="text" value="" class="input-text title">
                    </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        `);

        content.find('.title').change( (event) => {
            validateField($(event.target));
            this.title = $(event.target).val();
        } );

        this.variants.map( (variant) => {
            content.find('tbody').append(variant.getTemplate());
        } );

        content.find('tbody').append(this.createPlusButton());

        content.find('tbody').append(this.createStartButton());

        content.find("." + IDENTITIES.plusBtnClass).click(() => {
            this.createVariant();
        });

        $(this.blockSelector).prepend(content);

        this.content = content;
    }

    validate()
    {
        validateField($(this.content.find('.title')));

        this.variants.map( variant => variant.validate() );

        return objectFormValid(this.content);
    }

    toBackendModel()
    {
        const data = {
            question: {
                title: this.title
            },
            variants: []
        };

        this.variants.map( variant =>  data.variants.push(variant.toBackendModel()) );

        return data;
    }

    send()
    {
        const { data } = Api.request(ApiMethods.NewQuestion, this.toBackendModel(), 'post');
        this.questionUrl = ApiUrl + '/front/question.html?task=' + Task.Poll + '&question=' + data.question.uid;
        location.replace(this.questionUrl);
    }
}