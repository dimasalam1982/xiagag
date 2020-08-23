'use strict';

const Task = {
    CreateQuestion: 'new',
    Poll: 'question'
};


function parseLocation(param) {
    let url = location.search;
    let query = url.substr(1);
    let result = {};
    query.split("&").forEach(function(part) {
        let item = part.split("=");
        result[item[0]] = decodeURIComponent(item[1]);
    });
    return result;
}

const UrlParams = parseLocation()

let task = UrlParams['task'];

if (task === Task.Poll) {
    let question = UrlParams['question'];

    if (question) {
        $(document).ready(() => {
            const Poll = new UserPoll(question, 'div.poll');
            Poll.renderQuestion();

            const Statistic = new QuestionStatistic(question, 'div.poll');
        });
    }else{
        alert('Question param can not be empty. Please, verify your link.');
    }
}else if(task === Task.CreateQuestion) {
    const UserQuestion = new Question('div.poll');

    $(document).ready(() => {
        UserQuestion.render();
    });
}