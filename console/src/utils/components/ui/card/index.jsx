/**
 * Card component with optional rounded corners and default border/background styles.
 *
 * @param {object} props
 * @param {string} [props.className] - Additional class names to apply.
 * @param {boolean} [props.rounded=false] - Whether to apply rounded corners.
 * @param {React.ReactNode} props.children - Content inside the card.
 * @returns {JSX.Element}
 */
export const Card = ({ className = '', rounded = false, children }) => {
    return (
        <>
            <div
                className={`${
                    rounded ? 'rounded-2xl ' : ''
                } border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] ${className}`}
            >
                {children}
            </div>
        </>
    )
}

/**
 * CardHeader component for rendering the header section of a card.
 *
 * @param {object} props
 * @param {string} [props.className='px-3 py-2'] - Additional class names for styling.
 * @param {React.ReactNode} props.children - Content inside the card header.
 * @returns {JSX.Element}
 */
export const CardHeader = ({ className = 'px-3 py-2', children }) => {
    return <>{children && <div className={`border-b ${className}`}>{children}</div>}</>
}

/**
 * CardBody component for rendering the body section of a card.
 *
 * @param {object} props
 * @param {string} [props.className='px-3 py-2'] - Additional class names for styling.
 * @param {React.ReactNode} props.children - Content inside the card body.
 * @returns {JSX.Element}
 */
export const CardBody = ({ className = 'px-3 py-2', children }) => {
    return (
        <>
            <div className={`${className}`}>{children}</div>
        </>
    )
}

/**
 * CardFooter component for rendering the footer section of a card.
 *
 * @param {object} props
 * @param {string} [props.className='px-3 py-2'] - Additional class names for styling.
 * @param {React.ReactNode} props.children - Content inside the card footer.
 * @returns {JSX.Element}
 */
export const CardFooter = ({ className = 'px-3 py-2', children }) => {
    return (
        <>
            <div className={`border-t ${className}`}>{children}</div>
        </>
    )
}
