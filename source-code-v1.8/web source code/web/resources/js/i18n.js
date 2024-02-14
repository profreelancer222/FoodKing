import {createI18n} from "vue-i18n";

function loadMessages() {
    const context  = require.context("./languages", true, /[a-z0-9-_]+\.json$/i);
    const messages = context
        .keys()
        .map((key) => ({key, locale: key.match(/[a-z0-9-_]+/i)[0]}))
        .reduce((messages, {key, locale}) => ({
                ...messages, [locale]: context(key),
            }),
            {}
        );
    return {messages};
}

const {messages} = loadMessages();

const i18n = createI18n({
    legacy: false,
    locale: "en",
    fallbackLocale: "en",
    messages: messages
});

export default i18n;


