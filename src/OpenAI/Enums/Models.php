<?php
    namespace Game\OpenAI\Enums;
    use Game\Traits\EnumExtender\EnumExtender;
    enum Models: string {
        use EnumExtender;
        case DALLE2 = "dall-e-2";
        case DALLE3 = "dall-e-3";
        case GPT4 = "gpt-4";
        case GPT4o = "gpt-4o";
        case GPT35TURBO = "gpt-3.5-turbo";
        case GPT35TURBO1106 = "gpt-3.5-turbo-1106";
        case GPT35TURBO16K = "gpt-3.5-turbo-16k";

    }
