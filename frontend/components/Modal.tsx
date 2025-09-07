import React from 'react';
import ReactModal from 'react-modal';

ReactModal.setAppElement('#app');

const ModalContext = React.createContext<{ onClose: () => void } | null>(null);

interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    children: React.ReactNode;
}

export const Modal: React.FC<ModalProps> = ({ isOpen, onClose, children }) => (
    <ModalContext.Provider value={{ onClose }}>
        <ReactModal
            isOpen={isOpen}
            onRequestClose={onClose}
            shouldCloseOnOverlayClick
            closeTimeoutMS={100}
            preventScroll
            overlayClassName={{
                base: 'fixed inset-0 bg-black/0 transition-[background-color] duration-100 ease-out flex items-center justify-center p-4',
                afterOpen: 'bg-black/50',
                beforeClose: 'bg-black/0',
            }}
            className={{
                base: 'relative w-full max-w-lg bg-white rounded shadow-xl outline-none transform transition duration-200 ease-out opacity-0 scale-95',
                afterOpen: 'opacity-100 scale-100',
                beforeClose: 'opacity-0 scale-95',
            }}
            bodyOpenClassName="overflow-hidden"
            ariaHideApp
        >
            {children}
        </ReactModal>
    </ModalContext.Provider>
);

interface ModalHeaderProps {
    closeButton?: boolean;
    children: React.ReactNode;
}

export const ModalHeader: React.FC<ModalHeaderProps> = ({ closeButton, children }) => {
    const { onClose } = React.useContext(ModalContext)!;
    return (
        <div className="flex justify-between items-center p-4 border-b-1 border-gray-border">
            <div>{children}</div>
            {closeButton && (
                <button
                    className="px-3 py-2 -m-2 shrink-0 text-secondary hover:text-gray-700"
                    onClick={onClose}
                    aria-label="閉じる"
                >
                    <i className="ti ti-x" />
                </button>
            )}
        </div>
    );
};

interface ModalBodyProps {
    children: React.ReactNode;
}

export const ModalBody: React.FC<ModalBodyProps> = ({ children }) => <div className="p-4">{children}</div>;

interface ModalFooterProps {
    children: React.ReactNode;
}

export const ModalFooter: React.FC<ModalFooterProps> = ({ children }) => (
    <div className="flex justify-end p-4 gap-2 border-t-1 border-gray-border">{children}</div>
);
