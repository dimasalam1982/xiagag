const ApiUrl = 'http://localhost:8082';

const ApiMethods = {
    NewQuestion: 'question/create',
    QuestionDetail: 'question/show',
    Poll: 'poll/vote',
    Statistic: 'question/statistic'
};

const createUrlWithUid = (method, uid) => method + '/' + uid ;

class Api
{
    static request(methodURI, data, type = 'get')
    {
        let result;

        $.ajax({
            url: ApiUrl + '/' + methodURI,
            type: type,
            contentType: "application/json",
            dataType: 'json',
            async: false,
            data: JSON.stringify(data),
            success: (data) => {
                result = data;
            },
            error: (data) => {
                alert(data.responseJSON.data);
            }
        });

        return result;
    }
}