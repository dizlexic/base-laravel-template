import pluginJs from "@eslint/js";
import eslintConfigPrettier from "eslint-config-prettier";
import pluginVue from "eslint-plugin-vue";
import globals from "globals";

export default [
    {files: ["**/*.{js,mjs,cjs,vue}"]},
    {languageOptions: {globals: globals.browser}},
    pluginJs.configs.recommended,
    ...pluginVue.configs["flat/recommended"],
    eslintConfigPrettier,
    {
        rules: {
            "vue/no-unused-vars": "warn",
            "vue/no-unused-components": "warn",
            semi: ["error", "always"],
        },
    },
];
