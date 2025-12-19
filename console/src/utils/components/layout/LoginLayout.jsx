import { Outlet } from 'react-router'
import { ThemeToggleButton } from '../common/ThemeToggleButton'

export const LoginLayout = () => {
    return (
        <>
            <div className="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
                <div className="relative flex flex-col justify-center w-full h-screen lg:flex-row dark:bg-gray-900 sm:p-0">
                    <div className="flex flex-col flex-1">
                        <div className="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                            <div>
                                <Outlet />
                            </div>
                        </div>
                    </div>
                    <div className="items-center hidden w-full h-full lg:w-1/2 bg-blue-200 dark:bg-white/5 lg:grid">
                        <div className="relative flex items-center justify-center z-1">
                            <div className="flex flex-col items-center max-w-xs">
                                <p className="text-gray-900 dark:text-gray-100 text-4xl font-bold">
                                    abi-CMS
                                </p>
                                <p className="text-center text-gray-500 dark:text-white/60">
                                    abi-CMS is a headless CMS provided by Abilive.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="fixed z-50 hidden bottom-6 right-6 sm:block">
                        <ThemeToggleButton />
                    </div>
                </div>
            </div>
        </>
    )
}
