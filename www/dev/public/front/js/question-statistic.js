'use strict';

class QuestionStatistic
{
    questionUid;
    blockSelector;

    interval;

    constructor(questionUid, blockSelector) {

        this.questionUid = questionUid;
        this.blockSelector = blockSelector;

        //this.renderStat();

        this.interval = setInterval( () => {
            this.renderStat();
        }, 1000 );
    }

    renderStat()
    {
        const { data } = Api.request(createUrlWithUid(ApiMethods.Statistic, this.questionUid), {}, 'post');

        const content = $(
            `<div id="result">
                <h1>
                    Results
                </h1>
                <table class="ex2-table">
                <thead><tr><th>Name</th></tr></thead>
                <tbody></tbody>
            </table>
            </div>`);

        data.stat.map( stat => {
            content.find('tbody').append(`
                    <tr>
                        <td>${stat.name}</td>
                        ${data.question.variants.map( variant => `<td>${stat.variantId === variant.id ? 'X' : ''}</td>` )}
                    </tr>`);
        })

        data.question.variants.map( variant => content.find('thead tr').append(`<th>${variant.title}</th>`) )

        $(this.blockSelector).find('#result').remove();

        $(this.blockSelector).append(content);
    }
}