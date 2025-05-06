<?php
    namespace Game\OpenAI\Enums;
    use Game\Traits\EnumExtender\EnumExtender;
    enum Models {
        case DALLE2;
        case DALLE3;
        case GPT4;
        case GPT4o;
        case GPT35TURBO;
        case GPT35TURBO1106;
        case GPT35TURBO16K;

    }
