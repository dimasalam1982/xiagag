'use strict';

class UserPoll
{
    questionUid;
    question;
    blockSelector;
    userName;
    content;
    selectedVariantId;
    authUser;

    constructor(questionUid, blockSelector) {
        this.blockSelector = blockSelector;
        this.questionUid = questionUid;
        this.loadQuestion();
    }

    loadQuestion()
    {
        const { data } = Api.request(createUrlWithUid(ApiMethods.QuestionDetail,this.questionUid));

        this.question = data.question;
        this.authUser = data.user;

        if (this.authUser.name) {
            this.userName = this.authUser.name;
        }
    }

    renderQuestion()
    {
        const content = $(`
            <div>
                <h1>
                    ${this.question.title}
                </h1>
            
                <div class="ex2-question">
                    <div class="ex2-question__label">
                        Your name:
                    </div>
                    <div class="ex2-question__input">
                        <input type="text" value="${this.userName ? this.userName : ''}" class="input-text username">
                    </div>
                    <div class="ex2-question__answer"></div>
                    <div class="answer-hint">Please, choose answer</div>
                </div>
            </div>
        `);

        const submitButton = $(`
            <div class="ex2-question__submit">
                <input type="submit" class="btn" value="Submit">
            </div>
        `);

        submitButton.find('input').click( () => {
            this.send();
        } );

        content.find('.ex2-question').append(submitButton);

        this.question.variants.map( variant => {
            const checked = this.authUser.vote && this.authUser.vote.id === variant.id ? "checked" : "";

            if (checked === "checked") {
                this.selectedVariantId = variant.id;
            }

            const item = $(`
                <label>
                    <input type="radio" ${checked} name="do-we-go" data-id="${variant.id}">${variant.title}
                </label>
            `);
            item.find('input').click( () => this.checkSelectedVariant() );

            content.find('.ex2-question__answer').append(item);
        })

        content.find('.username').change( event => {
            this.userName = $(event.target).val();
            validateField($(event.target));
        } );

        this.content = content;

        this.blockAsnwers();

        $(this.blockSelector).append(content);

        return content;
    }

    checkSelectedVariant()
    {
        this.selectedVariantId = $('[name="do-we-go"]:checked').attr('data-id');
        const valid = !!this.selectedVariantId;

        if (valid) {
            this.content.find('.answer-hint').hide();
        }else{
            this.content.find('.answer-hint').show();
        }

        return valid;
    }

    validate()
    {
        validateField(this.content.find('.username'));

        if (!this.checkSelectedVariant()){
            return false;
        }

        return objectFormValid(this.content);
    }

    toBackend()
    {
        return{
            questionUid: this.questionUid,
            variantId: this.selectedVariantId,
            userName: this.userName
        }
    }

    send()
    {
        if (!this.validate()) {
            return false;
        }

        const data = this.toBackend();

        this.blockAsnwers();

        const response = Api.request(ApiMethods.Poll, data, 'post');
    }

    blockAsnwers()
    {
        if (this.selectedVariantId) {
            this.content.find('[type="radio"]').attr('disabled', true);
            this.content.find('[type="submit"]').attr('disabled', true).addClass('button-disabled');
        }
    }
}