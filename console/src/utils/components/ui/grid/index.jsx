const allowedCols = {
    1: 'grid-cols-1',
    2: 'grid-cols-2',
    3: 'grid-cols-3',
    4: 'grid-cols-4',
    5: 'grid-cols-5',
    6: 'grid-cols-6',
    7: 'grid-cols-7',
    8: 'grid-cols-8',
    9: 'grid-cols-9',
    10: 'grid-cols-10',
    11: 'grid-cols-11',
    12: 'grid-cols-12',
}

const allowedCol = {
    1: 'col-span-1',
    2: 'col-span-2',
    3: 'col-span-3',
    4: 'col-span-4',
    5: 'col-span-5',
    6: 'col-span-6',
    7: 'col-span-7',
    8: 'col-span-8',
    9: 'col-span-9',
    10: 'col-span-10',
    11: 'col-span-11',
    12: 'col-span-12',
}

/**
 * Row component that renders a CSS grid container with a given number of columns.
 *
 * @param {object} props
 * @param {string} [props.className] - Additional class names to apply.
 * @param {number} [props.cols=12] - Number of columns in the grid (Tailwind's `grid-cols-{n}`).
 * @param {React.Key} [props.key] - React key for the row.
 * @param {React.ReactNode} props.children - Child elements to render inside the grid.
 * @returns {JSX.Element}
 */
export const Row = ({ className = '', cols = 12, key = null, children }) => {
    let colsClass = allowedCols[cols] || 'grid-cols-12'
    return (
        <div className={`grid ${colsClass} ${className}`} key={key}>
            {children}
        </div>
    )
}

/**
 * Col component that renders a grid column span inside a Row.
 *
 * @param {object} props
 * @param {number|null} [props.col] - Number of columns to span (Tailwind's `col-span-{n}`).
 * @param {string} [props.className] - Additional class names to apply.
 * @param {React.Key} [props.key] - React key for the column.
 * @param {React.ReactNode} props.children - Child elements to render inside the column.
 * @returns {JSX.Element}
 */
export const Col = ({ col = null, className = '', key = null, children }) => {
    let colClass = allowedCol[col] || 'col-span-12'
    return (
        <div className={`${colClass} ${className}`} key={key}>
            {children}
        </div>
    )
}
