import { createRoot } from 'react-dom/client';

function Test() {
    return (
        <div>Testing</div>
    )
}

const root = createRoot(document.querySelector('.root'));
root.render(<Test />);