/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')


$(function () {
    swal.setDefaults({
        showCancelButton: true,
        animation: true,
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        showCloseButton: true,
    })

    $('a.external').click(function (e) {
        e.preventDefault(); // Prevent the href from redirecting directly
        var linkURL = $(this).attr("href");
        warnBeforeRedirect(linkURL);
    });

    $('a.invite-user').click(function (e) {
        e.preventDefault()
        inviteUser()
    })

    $('a.ban-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        var helpLink = $(this).attr("data-help");
        banUser(username, helpLink)
    })

    $('a.unban-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        var helpLink = $(this).attr("data-help");
        unbanUser(username, helpLink)
    })

    $('a.promote-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        var helpLink = $(this).attr("data-help");
        promoteUser(username, helpLink)
    })

    $('a.demote-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        var helpLink = $(this).attr("data-help");
        demoteUser(username, helpLink)
    })

    $('a.remove-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        var projectId = $(this).attr("data-project-id");
        var projectName = $(this).attr("data-project-name");
        var helpLink = $(this).attr("data-help");

        removeUser(username, projectId, projectName, helpLink)
    })

    $('a.add-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        var helpLink = $(this).attr("data-help");

        addUser(username, helpLink)
    })
});

function warnBeforeRedirect(linkURL) {
    swal({
        title: "Leave this site?",
        text: "If you click 'OK', you will be redirected to " + linkURL,
        type: "warning",
        timer: 5000,
        showCancelButton: true
    }).then(
        function () {
            window.location = linkURL;
        },
        function (dismiss) {
        }
    )
}

function inviteUser() {
    swal({
        title: 'Invite user via email.',
        input: 'email',
        inputAttributes: {
            autocomplete: 'off',
            autocorrect: 'off',
            autocapitalize: 'off',
            spellcheck: false
        },
        confirmButtonText: 'Invite',
        cancelButtonText: 'More options',
        preConfirm: function (email) {
            return new Promise(function (resolve, reject) {
                axios.post('/api/invitations', {'email': email}).then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    if (error.response.data.email != null && typeof error.response.data.email[0] != 'undefined')
                        reject(error.response.data.email[0])
                    reject(error)
                });
            })
        }
    }).then(function () {
        swal({
            type: 'success',
            title: 'Invitation sent!',
            timer: 2000,
            showCancelButton: false,
        }).then(
            function () {
            },
            function (dismiss) {
            }
        )
    }).catch(function (reason) {
        if (reason == 'cancel') {
            new route('invitations.create', {'email': swal.getInput().value}).run()
        }
    })
}

function banUser(username, helpLink) {
    swal({
        title: 'Ban user \'' + username + '\'.',
        html: 'Feel free to read <a href="' + helpLink + '">documentation</a>, before you proceed.',
        input: 'text',
        type: 'error',
        inputValue: 'User terribly misbehaved! End of story.',
        inputAttributes: {
            autocomplete: 'off',
            autocorrect: 'off',
            autocapitalize: 'off',
            spellcheck: true,
        },
        confirmButtonText: 'Ban',
        cancelButtonText: 'Cancel',
        preConfirm: function (text) {
            return new Promise(function (resolve, reject) {
                axios.patch('/api/users/' + username + '/ban', {'reason': text}).then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    if (error.response.data.reason != null && typeof error.response.data.reason[0] != 'undefined')
                        reject(error.response.data.reason[0])
                    reject(error)
                });
            })
        },
    }).then(function () {
        swal({
            type: 'success',
            title: 'User \'' + username + '\' was banned!',
            timer: 5000,
            showCancelButton: false,
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {
    })
}

function unbanUser(username, helpLink) {
    swal({
        title: 'Unban user \'' + username + '\'?',
        html: 'Feel free to read <a href="' + helpLink + '">documentation</a>, before you proceed.',
        type: 'question',
        confirmButtonText: 'Remove ban',
        cancelButtonText: 'Cancel',
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                axios.patch('/api/users/' + username + '/unban').then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    reject(error)
                });
            })
        },
    }).then(function () {
        swal({
            type: 'success',
            title: 'User \'' + username + '\' was unbanned!',
            timer: 5000,
            showCancelButton: false,
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {
    })
}

function promoteUser(username, helpLink) {
    swal({
        title: 'Promote user \'' + username + '\'?',
        html: 'Feel free to read <a href="' + helpLink + '">documentation</a>, before you proceed.',
        type: 'question',
        confirmButtonText: 'Promote',
        cancelButtonText: 'Cancel',
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                axios.patch('/api/users/' + username + '/promote').then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    reject(error)
                });
            })
        }
    }).then(function () {
        swal({
            type: 'success',
            title: 'User \'' + username + '\' was promoted!',
            timer: 5000,
            showCancelButton: false,
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {
    })
}

function demoteUser(username, helpLink) {
    swal({
        title: 'Demote user \'' + username + '\'?',
        html: 'Feel free to read <a href="' + helpLink + '">documentation</a>, before you proceed.',
        type: 'question',
        confirmButtonText: 'Demote',
        cancelButtonText: 'Cancel',
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                axios.patch('/api/users/' + username + '/demote').then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    reject(error)
                });
            })
        },
    }).then(function () {
        swal({
            type: 'success',
            title: 'User \'' + username + '\' was demoted!',
            timer: 5000,
            showCancelButton: false,
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {
    })
}

function removeUser(username, projectId, projectName, helpLink) {
    swal({
        title: 'Remove user \'' + username + '\' from project \'' + projectName + '\'?',
        html: 'Feel free to read <a href="' + helpLink + '">documentation</a>, before you proceed.',
        type: 'question',
        confirmButtonText: 'Remove',
        cancelButtonText: 'Cancel',
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                axios.delete('/api/projects/' + projectId + '/users/' + username).then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    reject(error)
                });
            })
        }
    }).then(function () {
        swal({
            type: 'success',
            title: 'User \'' + username + '\' is scheduled to be removed from project \'' + projectName + '\'!',
            timer: 5000,
            showCancelButton: false,
        }).then(
            function () {
                window.location.reload(false)
            },
            function (dismiss) {
                window.location.reload(false)
            }
        )
    }).catch(function (reason) {
    })
}

function addUser(username, helpLink) {
    var loadedProjects = new Promise(function (resolve, reject) {
        axios.get('/api/users/' + username + '/available-projects').then(function (response) {
            if (response.data.length == 0)
                reject('There are no available projects for user \'' + username + '\'.')
            resolve(response)
        }).catch(function (error) {
            console.log(error)
            reject('Sorry, this feature is now disabled.')
        });
    })

    loadedProjects.then(function (response) {

        var steps = [
            {
                progressSteps: ['1', '2'],
                animation: false,
                confirmButtonText: 'Continue <i class="fa fa-hand-o-right"></i>',
                html: 'Feel free to read <a href="' + helpLink + '">documentation</a>, before you proceed.',
                title: 'Participant or Team leader?',
                type: 'question',
                input: 'radio',
                inputValue: '0',
                inputOptions: {
                    0: 'Participant',
                    1: 'Team Leader'
                },
                inputValidator: function (role) {
                    return new Promise(function (resolve, reject) {
                        if (role == 0 || role == 1) {
                            resolve()
                        } else {
                            reject('You need to pick one!')
                        }
                    })
                },
                preConfirm: function (role) {
                    return new Promise(function (resolve) {
                        swal.insertQueueStep({
                            animation: false,
                            progressSteps: ['1', '2'],
                            title: 'What project?',
                            type: 'question',
                            input: 'select',
                            html: 'Feel free to read <a href="' + helpLink + '">documentation</a>, before you proceed.',
                            confirmButtonText: 'Add <i class="fa fa-thumbs-o-up"></i>',
                            inputOptions: response.data,
                            preConfirm: function (project) {
                                return new Promise(function (resolve, reject) {
                                    axios.post('/api/projects/' + project + '/users/' + username, {role: role}).then(function (response) {
                                        resolve(response)
                                    }).catch(function (error) {
                                        reject(error)
                                    });
                                })
                            }
                        })
                        resolve()
                    })
                }
            }
        ]

        swal.queue(steps).then(function (result) {
            swal({
                title: result[1].data.message,
                type: 'success',
                confirmButtonText: 'OK',
                showCancelButton: false,
                timer: 3000
            }).then(
                function () {
                    window.location.reload(false);
                },
                function (dismiss) {
                    window.location.reload(false);
                }
            )
        }, function () {
        })
    }).catch(function (error) {
        swal({
            title: error,
            text: 'For more information open up JavaScript console.',
            type: 'error',
            showCancelButton: false,
            confirmButtonText: 'Alright :('
        })
    })

}

function route(route, parameters = {}) {
    this.fetch = function () {
        return new Promise(function (resolve, reject) {
            axios('api/router/' + route + '/' + encodeURIComponent(JSON.stringify(parameters))).then(function (response) {
                resolve(response.data.url)
            }).catch(function (error) {
                reject(error)
            })
        })
    }

    var fetched = this.fetch()

    this.redirect = function () {
        fetched.then(function (url) {
            window.location = url;
        }).catch(function (error) {
        });
    }

    this.run = function () {
        return this.redirect()
    }
}
